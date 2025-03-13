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
        Schema::create('quarter_rooms', function (Blueprint $table) {
            $table->id();
            $table->ulid('token')->unique();
            $table->ulid('quarter_token')->comment('`quarters`.`token`')->index();
            $table->string('name', 20);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('quarter_token')->on('quarters')->references('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quater_room');
    }
};
