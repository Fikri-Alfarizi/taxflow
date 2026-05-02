<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk menyimpan laporan pajak yang di-generate
     * Sesuai UC-011 (Generate Laporan Pajak) di flowchart
     */
    public function up(): void
    {
        Schema::create('laporan_pajaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pajak_id')
                  ->constrained('pajaks')
                  ->cascadeOnDelete();

            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Periode laporan
            $table->date('periode_laporan');

            // Jenis laporan (per flowchart: PDF / Excel)
            $table->enum('jenis_laporan', ['PDF', 'Excel'])
                  ->default('PDF');

            // File path (stored di storage)
            $table->string('file_laporan')->nullable();

            // Laporan content (backup)
            $table->longText('konten_laporan')->nullable();

            // Timestamps
            $table->timestamp('tanggal_generate')->useCurrent();
            $table->timestamps();

            // Indices
            $table->index('pajak_id');
            $table->index('tanggal_generate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pajaks');
    }
};
