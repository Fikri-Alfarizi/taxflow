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
            $table->string('website')->nullable()->after('id_transaksi_source');
            $table->string('telepon_perusahaan')->nullable()->after('website');
            $table->string('email_perusahaan')->nullable()->after('telepon_perusahaan');
            $table->string('fax')->nullable()->after('email_perusahaan');
            $table->string('status_legal')->nullable()->after('fax');
            $table->string('klasifikasi_usaha')->nullable()->after('status_legal');
            $table->string('no_akta_pendirian')->nullable()->after('klasifikasi_usaha');
            $table->string('nib')->nullable()->after('no_akta_pendirian');
            $table->string('modal_kerja')->nullable()->after('nib');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pajaks', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'telepon_perusahaan',
                'email_perusahaan',
                'fax',
                'status_legal',
                'klasifikasi_usaha',
                'no_akta_pendirian',
                'nib',
                'modal_kerja',
            ]);
        });
    }
};
