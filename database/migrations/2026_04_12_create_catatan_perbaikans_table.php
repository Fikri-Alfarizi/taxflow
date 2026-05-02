<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk tracking catatan perbaikan/rejection notes
     * Sesuai flowmap: ketika data tidak lengkap, admin membuat catatan perbaikan
     * dan data dikembalikan ke staff untuk diperbaiki
     */
    public function up(): void
    {
        Schema::create('catatan_perbaikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pajak_id')
                  ->constrained('pajaks')
                  ->cascadeOnDelete();
            
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Catatan perbaikan dari admin (per flowmap)
            $table->text('catatan_perbaikan');

            // Status perbaikan (tracking progress)
            $table->enum('status', [
                'belum_diperbaiki',
                'sedang_diperbaiki',
                'selesai_diperbaiki'
            ])->default('belum_diperbaiki');

            // Timestamps
            $table->timestamp('tanggal_catatan')->useCurrent();
            $table->timestamp('tanggal_perbaikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_perbaikans');
    }
};
