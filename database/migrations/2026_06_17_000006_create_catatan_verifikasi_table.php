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
        Schema::create('catatan_verifikasi', function (Blueprint $table) {
            $table->id('id_catatan');
            $table->unsignedBigInteger('id_verifikasi')->index();
            $table->unsignedBigInteger('id_dokumen')->nullable()->index();
            $table->text('isi_catatan');
            $table->string('status_perbaikan', 50)->index();
            $table->timestamps();

            $table->foreign('id_verifikasi')
                ->references('id_verifikasi')
                ->on('verifikasi_berkas');

            $table->foreign('id_dokumen')
                ->references('id_dokumen')
                ->on('dokumen_perkara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_verifikasi');
    }
};
