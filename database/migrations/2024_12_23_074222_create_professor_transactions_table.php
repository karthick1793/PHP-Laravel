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
        Schema::create('professor_transactions', function (Blueprint $table) {

            $table->id();
            $table->ulid('token')->unique();
            $table->ulid('professor_token')->comment('`professors`.`token`')->index();
            $table->time('morning_time')->nullable();
            $table->float('morning_litre', 3, 1)->nullable();
            $table->time('evening_time')->nullable();
            $table->float('evening_litre', 3, 1)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('professor_token')->on('professors')->references('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_transactions');
    }
};
