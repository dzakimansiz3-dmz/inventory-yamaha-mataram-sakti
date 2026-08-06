<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {

            // status
            $table->enum('status', ['valid', 'dibatalkan'])
                  ->default('valid')
                  ->after('tanggal');

            // is_late
            $table->boolean('is_late')
                  ->default(false)
                  ->after('status');

            // keterangan (kalau belum ada)
            $table->string('keterangan')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_late', 'keterangan']);
        });
    }
};