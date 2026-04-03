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
            $table->string('npwp')->nullable()->after('nama_perusahaan');
            $table->text('alamat_lengkap')->nullable()->after('npwp');
            $table->string('nama_pic')->nullable()->after('alamat_lengkap');
            $table->string('kontak_pic')->nullable()->after('nama_pic');
            $table->string('email_pic')->nullable()->after('kontak_pic');
            $table->string('kpp_pratama')->nullable()->after('email_pic');
            $table->string('kategori_usaha')->nullable()->after('kpp_pratama');
            $table->string('id_transaksi_source')->nullable()->after('kategori_usaha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->dropColumn([
                'npwp',
                'alamat_lengkap',
                'nama_pic',
                'kontak_pic',
                'email_pic',
                'kpp_pratama',
                'kategori_usaha',
                'id_transaksi_source'
            ]);
        });
    }
};
