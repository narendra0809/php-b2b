<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sightseeings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');  
            $table->string('contact_no');  
            $table->string('address');
            $table->string('email')->unique();
            $table->string('description');
            $table->string('scompany_document');
            $table->string('s_pic');
            $table->decimal('rate_adult', 8, 2);
            $table->decimal('rate_child', 8, 2);
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sightseeings');
    }
};