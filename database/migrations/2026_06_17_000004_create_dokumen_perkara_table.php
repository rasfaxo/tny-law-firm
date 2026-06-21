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
        Schema::create('dokumen_perkara', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->unsignedBigInteger('id_pendaftaran')->index();
            $table->string('nama_dokumen', 150);
            $table->string('jenis_dokumen', 50);
            $table->string('file_path');
            $table->string('status_dokumen', 50)->index();
            $table->timestamps();

            $table->foreign('id_pendaftaran')
                ->references('id_pendaftaran')
                ->on('pra_pendaftaran_perkara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_perkara');
    }
};
