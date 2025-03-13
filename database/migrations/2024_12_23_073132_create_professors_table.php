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
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->ulid('token')->unique();
            $table->string('name', 100);
            $table->string('country_code', 10);
            $table->string('mobile_number', 30);
            $table->string('image')->nullable();
            $table->ulid('room_token')->comment('`quarter_rooms`.`token`')->index();
            $table->integer('available_coin_count');
            $table->string('otp', 10)->nullable();
            $table->dateTime('otp_sms_valid_time')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('room_token')->on('quarter_rooms')->references('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professors');
    }
};
