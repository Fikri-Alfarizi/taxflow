<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan validation fields ke dokumens table
     * Sesuai UC-008 (Validasi Dokumen)
     */
    public function up(): void
    {
        Schema::table('dokumens', function (Blueprint $table) {
            // Validation status (per UC-008)
            $table->enum('status_validasi', ['pending', 'valid', 'invalid'])
                  ->default('pending')
                  ->after('file_dokumen');
            
            // Who validated
            $table->foreignId('validated_by')->nullable()
                  ->constrained('users')
                  ->nullableOnDelete()
                  ->after('tanggal_upload');

            // Validation timestamp
            $table->timestamp('tanggal_validasi')->nullable()
                  ->after('validated_by');

            // Validation reason (jika invalid)
            $table->text('keterangan_validasi')->nullable()
                  ->after('tanggal_validasi');

            // File type (untuk validate dokumen type)
            $table->string('tipe_file')->nullable()
                  ->after('file_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumens', function (Blueprint $table) {
            $table->dropColumn([
                'status_validasi',
                'validated_by',
                'tanggal_validasi',
                'keterangan_validasi',
                'tipe_file',
            ]);
        });
    }
};
