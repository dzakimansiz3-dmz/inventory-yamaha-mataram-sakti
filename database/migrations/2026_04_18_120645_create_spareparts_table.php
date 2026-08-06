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
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id('id_sparepart');
            $table->string('nama_sparepart');
            $table->string('kode_part')->unique();
            $table->string('kategori');
            $table->integer('harga');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
