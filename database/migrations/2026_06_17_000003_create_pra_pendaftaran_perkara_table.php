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
        Schema::create('pra_pendaftaran_perkara', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->unsignedBigInteger('id_user')->index();
            $table->unsignedBigInteger('id_kategori')->index();
            $table->string('judul_perkara', 150);
            $table->text('kronologi');
            $table->string('status_pengajuan', 50)->index();
            $table->dateTime('tanggal_pengajuan')->index();
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users');

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_perkara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pra_pendaftaran_perkara');
    }
};
