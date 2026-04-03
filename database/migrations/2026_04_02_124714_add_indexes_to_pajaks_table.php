<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->index('nama_perusahaan');
            $table->index('jenis_pajak');
            $table->index('status');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->dropIndex(['nama_perusahaan']);
            $table->dropIndex(['jenis_pajak']);
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_jatuh_tempo']);
        });
    }
};
