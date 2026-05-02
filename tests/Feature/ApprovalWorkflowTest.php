<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pajak;
use App\Models\Dokumen;
use App\Models\CatatanPerbaikan;
use App\Models\LaporanPajak;
use App\Models\Monitoring;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $staff;
    private $pajak;
    private $dokumen;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        // Create test pajak
        $this->pajak = Pajak::factory()->create([
            'user_id' => $this->staff->id,
            'status_verifikasi' => 'pending',
            'status_validasi' => 'pending',
            'status_approval' => 'pending',
        ]);

        // Create test dokumen
        $this->dokumen = Dokumen::factory()->create([
            'pajak_id' => $this->pajak->id,
            'status_validasi' => 'pending',
        ]);
    }

    /** TEST UC-007: VERIFIKASI DATA PAJAK **/
    public function test_admin_can_verifikasi_data_pajak()
    {
        $this->actingAs($this->admin);

        $response = $this->post("/approval/verifikasi/{$this->pajak->id}", [
            'status_verifikasi' => 'verified',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_verifikasi' => 'verified',
            'verified_by' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('monitorings', [
            'pajak_id' => $this->pajak->id,
            'user_id' => $this->admin->id,
            'status_proses' => 'verified',
        ]);
    }

    public function test_admin_can_request_revisi_data_pajak()
    {
        $this->actingAs($this->admin);

        $catatan = 'NPWP tidak valid, silakan perbaiki';

        $response = $this->post("/approval/verifikasi/{$this->pajak->id}", [
            'status_verifikasi' => 'needs_revision',
            'catatan_perbaikan' => $catatan,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_verifikasi' => 'needs_revision',
            'status_validasi' => 'pending',
            'status_approval' => 'pending',
        ]);
        $this->assertDatabaseHas('catatan_perbaikans', [
            'pajak_id' => $this->pajak->id,
            'created_by' => $this->admin->id,
            'catatan_perbaikan' => $catatan,
            'status' => 'belum_diperbaiki',
        ]);
    }

    public function test_verifikasi_fails_if_already_verified()
    {
        $this->pajak->update(['status_verifikasi' => 'verified']);
        $this->actingAs($this->admin);

        $response = $this->post("/approval/verifikasi/{$this->pajak->id}", [
            'status_verifikasi' => 'verified',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** TEST UC-008: VALIDASI DOKUMEN **/
    public function test_admin_can_validate_dokumen()
    {
        $this->pajak->update(['status_verifikasi' => 'verified']);
        $this->actingAs($this->admin);

        $response = $this->post("/approval/validasi-dokumen/{$this->dokumen->id}", [
            'status_validasi' => 'valid',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dokumens', [
            'id' => $this->dokumen->id,
            'status_validasi' => 'valid',
            'validated_by' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_validasi' => 'valid',
        ]);
    }

    public function test_dokumen_validation_fails_if_pajak_not_verified()
    {
        $this->actingAs($this->admin);

        $response = $this->post("/approval/validasi-dokumen/{$this->dokumen->id}", [
            'status_validasi' => 'valid',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_batch_dokumen_validation_updates_pajak_status()
    {
        $this->pajak->update(['status_verifikasi' => 'verified']);

        // Create multiple dokumen
        $dokumen2 = Dokumen::factory()->create([
            'pajak_id' => $this->pajak->id,
            'status_validasi' => 'pending',
        ]);

        $this->actingAs($this->admin);

        // Validate first dokumen as valid
        $this->post("/approval/validasi-dokumen/{$this->dokumen->id}", [
            'status_validasi' => 'valid',
        ]);

        // Pajak status should still be pending
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_validasi' => 'pending',
        ]);

        // Validate second dokumen as invalid
        $this->post("/approval/validasi-dokumen/{$dokumen2->id}", [
            'status_validasi' => 'invalid',
            'keterangan_validasi' => 'Dokumen tidak lengkap',
        ]);

        // Pajak status should be invalid
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_validasi' => 'invalid',
        ]);
    }

    /** TEST UC-009: APPROVE/REJECT PROSES PAJAK **/
    public function test_admin_can_approve_pajak()
    {
        $this->pajak->update([
            'status_verifikasi' => 'verified',
            'status_validasi' => 'valid',
        ]);
        $this->actingAs($this->admin);

        $response = $this->post("/approval/approve-reject/{$this->pajak->id}", [
            'status_approval' => 'approved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_approval' => 'approved',
            'status' => 'selesai',
            'approved_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_reject_pajak()
    {
        $this->pajak->update([
            'status_verifikasi' => 'verified',
            'status_validasi' => 'valid',
        ]);
        $this->actingAs($this->admin);

        $response = $this->post("/approval/approve-reject/{$this->pajak->id}", [
            'status_approval' => 'rejected',
            'keterangan' => 'Dokumen tidak sesuai ketentuan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_approval' => 'rejected',
            'status' => 'ditolak',
            'approved_by' => $this->admin->id,
        ]);
    }

    public function test_approval_fails_if_not_validated()
    {
        $this->actingAs($this->admin);

        $response = $this->post("/approval/approve-reject/{$this->pajak->id}", [
            'status_approval' => 'approved',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** TEST UC-011: GENERATE LAPORAN PAJAK **/
    public function test_admin_can_generate_pdf_laporan()
    {
        Storage::fake('public');
        $this->pajak->update([
            'status_verifikasi' => 'verified',
            'status_validasi' => 'valid',
            'status_approval' => 'approved',
        ]);
        $this->actingAs($this->admin);

        $response = $this->post("/approval/generate-laporan/{$this->pajak->id}", [
            'jenis_laporan' => 'PDF',
            'periode_laporan' => '2026-04-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('laporan_pajaks', [
            'pajak_id' => $this->pajak->id,
            'generated_by' => $this->admin->id,
            'jenis_laporan' => 'PDF',
        ]);
        Storage::disk('public')->assertExists('laporan-pajak/laporan-pajak-' . $this->pajak->id . '-2026-04-12-22-31-47.PDF');
    }

    public function test_laporan_generation_fails_if_not_approved()
    {
        $this->actingAs($this->admin);

        $response = $this->post("/approval/generate-laporan/{$this->pajak->id}", [
            'jenis_laporan' => 'PDF',
            'periode_laporan' => '2026-04-01',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** TEST PERMISSION CONTROL **/
    public function test_staff_cannot_access_approval_dashboard()
    {
        $this->actingAs($this->staff);

        $response = $this->get('/approval/dashboard');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_verifikasi_data()
    {
        $this->actingAs($this->staff);

        $response = $this->post("/approval/verifikasi/{$this->pajak->id}", [
            'status_verifikasi' => 'verified',
        ]);

        $response->assertStatus(403);
    }

    /** TEST STAFF PERBAIKAN WORKFLOW **/
    public function test_staff_can_mark_perbaikan_as_completed()
    {
        $catatan = CatatanPerbaikan::factory()->create([
            'pajak_id' => $this->pajak->id,
            'created_by' => $this->admin->id,
            'status' => 'belum_diperbaiki',
        ]);

        $this->actingAs($this->staff);

        $response = $this->post("/catatan-perbaikan/{$catatan->id}/selesai");

        $response->assertRedirect();
        $this->assertDatabaseHas('catatan_perbaikans', [
            'id' => $catatan->id,
            'status' => 'selesai_diperbaiki',
            'tanggal_perbaikan' => now(),
        ]);
    }

    public function test_staff_cannot_mark_perbaikan_of_other_pajak()
    {
        $otherPajak = Pajak::factory()->create(['user_id' => User::factory()->create()->id]);
        $catatan = CatatanPerbaikan::factory()->create([
            'pajak_id' => $otherPajak->id,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->staff);

        $response = $this->post("/catatan-perbaikan/{$catatan->id}/selesai");

        $response->assertStatus(403);
    }

    /** TEST DASHBOARD STATISTICS **/
    public function test_approval_dashboard_shows_correct_statistics()
    {
        // Create test data
        Pajak::factory()->create(['status_verifikasi' => 'pending']);
        Pajak::factory()->create(['status_verifikasi' => 'verified', 'status_validasi' => 'pending']);
        Pajak::factory()->create(['status_verifikasi' => 'verified', 'status_validasi' => 'valid', 'status_approval' => 'pending']);

        $this->actingAs($this->admin);

        $response = $this->get('/approval/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Pending Verifikasi');
        $response->assertSee('Pending Validasi');
        $response->assertSee('Pending Approval');
    }

    /** TEST END-TO-END APPROVAL WORKFLOW **/
    public function test_complete_approval_workflow()
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        // Step 1: Verifikasi
        $this->post("/approval/verifikasi/{$this->pajak->id}", [
            'status_verifikasi' => 'verified',
        ]);

        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_verifikasi' => 'verified',
        ]);

        // Step 2: Validasi Dokumen
        $this->post("/approval/validasi-dokumen/{$this->dokumen->id}", [
            'status_validasi' => 'valid',
        ]);

        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_validasi' => 'valid',
        ]);

        // Step 3: Approve
        $this->post("/approval/approve-reject/{$this->pajak->id}", [
            'status_approval' => 'approved',
        ]);

        $this->assertDatabaseHas('pajaks', [
            'id' => $this->pajak->id,
            'status_approval' => 'approved',
            'status' => 'selesai',
        ]);

        // Step 4: Generate Laporan
        $this->post("/approval/generate-laporan/{$this->pajak->id}", [
            'jenis_laporan' => 'PDF',
            'periode_laporan' => '2026-04-01',
        ]);

        $this->assertDatabaseHas('laporan_pajaks', [
            'pajak_id' => $this->pajak->id,
            'jenis_laporan' => 'PDF',
        ]);
    }
}
