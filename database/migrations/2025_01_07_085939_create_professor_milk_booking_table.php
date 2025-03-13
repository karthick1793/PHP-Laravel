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
        Schema::create('professor_milk_booking', function (Blueprint $table) {
            $table->id();
            $table->ulid('professor_token')->comment('`professors`.`token`');
            $table->enum('time_slot', ['Morning', 'Evening'])->comment('`Morning`,`Evening`');
            $table->date('delivery_date');
            $table->float('quantity', 3, 1)->comment('milk quantity');
            $table->enum('status', ['Pending', 'Cancelled', 'Delivered', 'Not Delivered'])->comment('`Pending`,`Cancelled`,`Delivered`,`Not Delivered`');
            $table->timestamps();

            $table->foreign('professor_token')->on('professors')->references('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_milk_booking');
    }
};
