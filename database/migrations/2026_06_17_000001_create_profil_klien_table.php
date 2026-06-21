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
        Schema::create('profil_klien', function (Blueprint $table) {
            $table->id('id_profil');
            $table->unsignedBigInteger('id_user')->unique();
            $table->text('alamat')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('no_identitas', 50)->nullable();
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
        Schema::dropIfExists('profil_klien');
    }
};
