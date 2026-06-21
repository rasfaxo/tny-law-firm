<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentionally empty: cache tables are outside the locked thesis schema.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally empty: this migration does not create cache tables.
    }
};
