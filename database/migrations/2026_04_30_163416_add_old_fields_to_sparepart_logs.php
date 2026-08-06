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
        Schema::table('sparepart_logs', function (Blueprint $table) {
            $table->string('old_nama_sparepart')->nullable();
            $table->string('old_kode_part')->nullable();
            $table->string('old_kategori')->nullable();
            $table->bigInteger('old_harga')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('sparepart_logs', function (Blueprint $table) {
            $table->dropColumn([
                'old_nama_sparepart',
                'old_kode_part',
                'old_kategori',
                'old_harga'
            ]);
        });
    }
};
