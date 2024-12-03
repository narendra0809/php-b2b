<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bankdetails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to the users table
            $table->string('account_details')->nullable(); // For account number or other details
            $table->string('upi_id')->nullable(); // UPI ID for digital transactions
            $table->string('bank_name'); // Name of the bank
            $table->string('account_holder_name'); // Name of the account holder
            $table->string('ifsc_code'); // IFSC code of the bank
            $table->enum('account_type', ['savings', 'current', 'salary'])->default('savings'); // Account type options
            $table->string('branch'); // Bank branch name
            $table->enum('status', ['online', 'offline'])->default('online'); // Status of the bank account
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bankdetails');
    }
};
