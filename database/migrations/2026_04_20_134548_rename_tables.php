<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::rename('barang_keluars', 'barang_keluar');
        Schema::rename('barang_masuks', 'barang_masuk');
    }

    public function down()
    {
        Schema::rename('barang_keluar', 'barang_keluars');
        Schema::rename('barang_masuk', 'barang_masuks');
    }
};
