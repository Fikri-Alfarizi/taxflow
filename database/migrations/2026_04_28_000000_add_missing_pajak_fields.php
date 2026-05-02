<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan SEMUA kolom yang hilang dari model Pajak ke database
     * Kolom-kolom ini sudah ada di $fillable tapi belum ada di database
     */
    public function up(): void
    {
        // Ambil semua kolom yang sudah ada di tabel pajaks
        $existingColumns = DB::getSchemaBuilder()->getColumnListing('pajaks');
        
        Schema::table('pajaks', function (Blueprint $table) use ($existingColumns) {
            // Profil Perusahaan - kolom yang mungkin belum ada
            if (!in_array('status_perusahaan', $existingColumns)) {
                $table->string('status_perusahaan')->nullable()->after('nama_perusahaan');
            }
            if (!in_array('no_akta', $existingColumns)) {
                $table->string('no_akta')->nullable()->after('nib');
            }
            if (!in_array('sk_kemenkumham', $existingColumns)) {
                $table->string('sk_kemenkumham')->nullable()->after('no_akta');
            }
            if (!in_array('tahun_berdiri', $existingColumns)) {
                $table->integer('tahun_berdiri')->nullable()->after('sk_kemenkumham');
            }
            
            // PIC Details
            if (!in_array('jabatan_pic', $existingColumns)) {
                $table->string('jabatan_pic')->nullable()->after('nama_pic');
            }
            
            // Logo
            if (!in_array('logo_url', $existingColumns)) {
                $table->string('logo_url')->nullable()->after('modal_kerja');
            }
            
            // Approval workflow fields
            if (!in_array('status_verifikasi', $existingColumns)) {
                $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->nullable()->after('status');
            }
            if (!in_array('status_validasi', $existingColumns)) {
                $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->nullable()->after('status_verifikasi');
            }
            if (!in_array('status_approval', $existingColumns)) {
                $table->enum('status_approval', ['pending', 'approved', 'rejected'])->nullable()->after('status_validasi');
            }
            if (!in_array('verified_by', $existingColumns)) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('status_approval');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!in_array('approved_by', $existingColumns)) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('verified_by');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!in_array('tanggal_verifikasi', $existingColumns)) {
                $table->timestamp('tanggal_verifikasi')->nullable()->after('approved_by');
            }
            if (!in_array('tanggal_validasi', $existingColumns)) {
                $table->timestamp('tanggal_validasi')->nullable()->after('tanggal_verifikasi');
            }
            if (!in_array('tanggal_approval', $existingColumns)) {
                $table->timestamp('tanggal_approval')->nullable()->after('tanggal_validasi');
            }
            if (!in_array('keterangan_penolakan', $existingColumns)) {
                $table->text('keterangan_penolakan')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status_perusahaan',
                'no_akta',
                'sk_kemenkumham',
                'tahun_berdiri',
                'jabatan_pic',
                'logo_url',
                'status_verifikasi',
                'status_validasi',
                'status_approval',
                'verified_by',
                'approved_by',
                'tanggal_verifikasi',
                'tanggal_validasi',
                'tanggal_approval',
                'keterangan_penolakan',
            ]);
        });
    }
};