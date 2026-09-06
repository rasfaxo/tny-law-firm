<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_reschedule', function (Blueprint $table) {
            $table->unsignedBigInteger('id_admin_keputusan')
                ->nullable()
                ->after('id_user');
            $table->foreign('id_admin_keputusan')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_reschedule', function (Blueprint $table) {
            $table->dropForeign(['id_admin_keputusan']);
            $table->dropColumn('id_admin_keputusan');
        });
    }
};
