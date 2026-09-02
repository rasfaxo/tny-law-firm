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
        Schema::table('permintaan_reschedule', function (Blueprint $table) {
            $table->index(['id_user', 'status_reschedule'], 'idx_user_status_reschedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_reschedule', function (Blueprint $table) {
            $table->dropIndex('idx_user_status_reschedule');
        });
    }
};
