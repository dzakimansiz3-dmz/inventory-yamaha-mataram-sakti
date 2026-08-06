<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SPAREPART
        Schema::table('spareparts', function (Blueprint $table) {

            // user_id
            if (Schema::hasColumn('spareparts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            // gambar
            if (Schema::hasColumn('spareparts', 'gambar')) {
                $table->dropColumn('gambar');
            }
        });

        // BARANG MASUK
        Schema::table('barang_masuk', function (Blueprint $table) {

            if (Schema::hasColumn('barang_masuk', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('barang_masuk', 'is_late')) {
                $table->dropColumn('is_late');
            }
        });

        // BARANG KELUAR
        Schema::table('barang_keluar', function (Blueprint $table) {

            if (Schema::hasColumn('barang_keluar', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('barang_keluar', 'is_late')) {
                $table->dropColumn('is_late');
            }
        });

        // SPAREPART LOG
        Schema::table('sparepart_logs', function (Blueprint $table) {

            if (Schema::hasColumn('sparepart_logs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        //
    }
};