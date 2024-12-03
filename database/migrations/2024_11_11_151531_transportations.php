<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transportations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade'); // References the 'destinations' table
            $table->string('company_name', 255);
            $table->string('company_document')->nullable(); // Path to the document file
            $table->string('email')->unique();
            $table->string('contact_no', 15);
            $table->string('address', 255);
            $table->string('mode_of_transportation', 100); // Example: Air, Bus, Train, etc.
            $table->string('vehicle_type', 100); // Example: Car, SUV, Bus, etc.
            $table->json('options')->nullable(); // Store transport options as JSON (e.g., [ { "option": "AC", "rate": 1250 }, { "option": "Non-AC", "rate": 700 } ])
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transportations');
    }
};
