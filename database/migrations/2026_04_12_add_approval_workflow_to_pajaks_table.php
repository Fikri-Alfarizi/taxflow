<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan approval workflow fields ke pajaks table
     * Sesuai UC-007 (Verifikasi), UC-008 (Validasi), UC-009 (Approval)
     */
    public function up(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            // Status workflow (per flowmap & flowchart dokumentasi)
            $table->enum('status_verifikasi', ['pending', 'verified', 'needs_revision'])
                  ->default('pending')
                  ->after('status');
            
            $table->enum('status_validasi', ['pending', 'valid', 'invalid'])
                  ->default('pending')
                  ->after('status_verifikasi');
            
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('status_validasi');

            // Approval tracking
            $table->foreignId('verified_by')->nullable()
                  ->constrained('users')
                  ->nullableOnDelete()
                  ->after('user_id');
            
            $table->foreignId('approved_by')->nullable()
                  ->constrained('users')
                  ->nullableOnDelete()
                  ->after('verified_by');

            // Approval timestamps
            $table->timestamp('tanggal_verifikasi')->nullable()
                  ->after('tanggal_jatuh_tempo');
            
            $table->timestamp('tanggal_validasi')->nullable()
                  ->after('tanggal_verifikasi');
            
            $table->timestamp('tanggal_approval')->nullable()
                  ->after('tanggal_validasi');

            // Rejection reason (per flowmap - catatan perbaikan)
            $table->text('keterangan_penolakan')->nullable()
                  ->after('keterangan');

            // Detail jenis pajak (per kamus data)
            $table->text('jenis_pajak_rincian')->nullable()
                  ->after('jenis_pajak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi',
                'status_validasi',
                'status_approval',
                'verified_by',
                'approved_by',
                'tanggal_verifikasi',
                'tanggal_validasi',
                'tanggal_approval',
                'keterangan_penolakan',
                'jenis_pajak_rincian',
            ]);
        });
    }
};
