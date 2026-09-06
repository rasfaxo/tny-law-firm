<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_consents', function (Blueprint $table) {
            $table->id('id_consent');
            $table->unsignedBigInteger('id_user');
            $table->string('policy_version', 50);
            $table->timestamp('agreed_at');
            $table->timestamps();

            $table->unique(['id_user', 'policy_version']);
            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_consents');
    }
};
