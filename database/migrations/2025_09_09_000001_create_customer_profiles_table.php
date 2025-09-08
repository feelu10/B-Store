<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basic profile
            $table->string('first_name', 120)->nullable();
            $table->string('last_name', 120)->nullable();
            $table->string('phone', 20)->nullable(); // store normalized +1XXXXXXXXXX

            // Shipping (US)
            $table->string('ship_full_name', 160)->nullable();
            $table->string('ship_line1', 255)->nullable();
            $table->string('ship_line2', 255)->nullable();
            $table->string('ship_city', 160)->nullable();
            $table->string('ship_state', 2)->nullable(); // 2-letter
            $table->string('ship_postal_code', 10)->nullable(); // 12345 or 12345-6789
            $table->string('ship_country', 120)->default('United States');
            $table->string('ship_landmark', 255)->nullable(); // delivery notes
            $table->boolean('ship_is_default')->default(true);

            // Billing (optional)
            $table->boolean('has_separate_billing')->default(false);
            $table->string('bill_full_name', 160)->nullable();
            $table->string('bill_line1', 255)->nullable();
            $table->string('bill_line2', 255)->nullable();
            $table->string('bill_city', 160)->nullable();
            $table->string('bill_state', 2)->nullable();
            $table->string('bill_postal_code', 10)->nullable();
            $table->string('bill_country', 120)->default('United States');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
