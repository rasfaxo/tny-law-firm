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
        Schema::create('jadwal_konsultasi', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->unsignedBigInteger('id_user')->index();
            $table->date('tanggal')->index();
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('status_slot', 30)->index();
            $table->timestamps();

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
        Schema::dropIfExists('jadwal_konsultasi');
    }
};
