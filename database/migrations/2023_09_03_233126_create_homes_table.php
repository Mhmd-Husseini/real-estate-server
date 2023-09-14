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
        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->integer('rooms_nb')->nullable();
            $table->integer('balconies_nb')->nullable();
            $table->integer('bathrooms_nb')->nullable();
            $table->integer('garages_nb')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
