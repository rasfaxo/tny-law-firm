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
        Schema::create('booking_konsultasi', function (Blueprint $table) {
            $table->id('id_booking');
            $table->unsignedBigInteger('id_pendaftaran')->index();
            $table->unsignedBigInteger('id_jadwal')->index();
            $table->unsignedBigInteger('id_user')->index();
            $table->string('status_booking', 30)->index();
            $table->dateTime('tanggal_booking')->index();
            $table->timestamps();

            $table->foreign('id_pendaftaran')
                ->references('id_pendaftaran')
                ->on('pra_pendaftaran_perkara');

            $table->foreign('id_jadwal')
                ->references('id_jadwal')
                ->on('jadwal_konsultasi');

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
        Schema::dropIfExists('booking_konsultasi');
    }
};
