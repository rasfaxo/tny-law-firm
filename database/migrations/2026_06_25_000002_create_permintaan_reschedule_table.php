<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("permintaan_reschedule", function (Blueprint $table) {
            $table->id("id_reschedule");
            $table->unsignedBigInteger("id_booking");
            $table->unsignedBigInteger("id_user");
            $table->text("alasan_reschedule");
            $table->text("preferensi_jadwal")->nullable();
            $table->string("preferensi_metode", 20)->nullable();
            $table
                ->string("status_reschedule", 30)
                ->default("menunggu_persetujuan");
            $table->unsignedBigInteger("id_jadwal_baru")->nullable();
            $table->unsignedBigInteger("id_booking_baru")->nullable();
            $table->text("catatan_admin")->nullable();
            $table->timestamp("tanggal_pengajuan");
            $table->timestamp("tanggal_keputusan")->nullable();
            $table->timestamps();

            $table
                ->foreign("id_booking")
                ->references("id_booking")
                ->on("booking_konsultasi")
                ->cascadeOnDelete();

            $table
                ->foreign("id_user")
                ->references("id_user")
                ->on("users")
                ->cascadeOnDelete();

            $table
                ->foreign("id_jadwal_baru")
                ->references("id_jadwal")
                ->on("jadwal_konsultasi")
                ->nullOnDelete();

            $table
                ->foreign("id_booking_baru")
                ->references("id_booking")
                ->on("booking_konsultasi")
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("permintaan_reschedule");
    }
};
