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
        Schema::table('booking_konsultasi', function (Blueprint $table) {
            $table->string('metode_konsultasi', 20)->default('offline')->after('status_booking');
            $table->string('status_konfirmasi_konsultasi', 30)->default('menunggu_konfirmasi')->after('metode_konsultasi');
            $table->string('link_konsultasi')->nullable()->after('status_konfirmasi_konsultasi');
            $table->string('lokasi_konsultasi')->nullable()->after('link_konsultasi');
            $table->text('catatan_konsultasi')->nullable()->after('lokasi_konsultasi');
            $table->text('catatan_preferensi_klien')->nullable()->after('catatan_konsultasi');
            $table->timestamp('dikonfirmasi_pada')->nullable()->after('catatan_preferensi_klien');
            $table->unsignedBigInteger('id_admin_konfirmasi')->nullable()->after('dikonfirmasi_pada');

            $table->foreign('id_admin_konfirmasi')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_konsultasi', function (Blueprint $table) {
            $table->dropForeign(['id_admin_konfirmasi']);
            $table->dropColumn([
                'metode_konsultasi',
                'status_konfirmasi_konsultasi',
                'link_konsultasi',
                'lokasi_konsultasi',
                'catatan_konsultasi',
                'catatan_preferensi_klien',
                'dikonfirmasi_pada',
                'id_admin_konfirmasi',
            ]);
        });
    }
};
