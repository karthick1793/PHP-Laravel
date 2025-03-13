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
        Schema::create('professor_logs', function (Blueprint $table) {
            $table->id();
            $table->ulid('token')->unique();
            $table->ulid('professor_token')->comment('`professors`.`token`')->index();
            $table->integer('old_coin_count');
            $table->integer('added_coin_count');
            $table->integer('total_coin_count');
            $table->timestamps();

            $table->foreign('professor_token')->on('professors')->references('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_logs');
    }
};
