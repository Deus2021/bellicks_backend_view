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
        Schema::create('customer_sms', function (Blueprint $table) {
            $table->id('sms_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('repayment_id');
            $table->text('sms_body');
            $table->text('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sms');
    }
};
