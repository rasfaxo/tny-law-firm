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
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->unsignedBigInteger('id_pendaftaran')->index();
            $table->unsignedBigInteger('id_user')->index();
            $table->string('status', 50)->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_pendaftaran')
                ->references('id_pendaftaran')
                ->on('pra_pendaftaran_perkara');

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
