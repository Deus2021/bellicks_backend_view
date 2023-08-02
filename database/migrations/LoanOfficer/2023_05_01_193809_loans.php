<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id('loan_id');
            $table->unsignedBigInteger('loan_type_id');
//            $table->string('loan_amount');
            // $table->string('form_cost');
            // $table->integer('loan_amount');
//             $table->string('form_cost');
//             $table->integer('loan_amount');

            $table->unsignedBigInteger('customer_id');
//            $table->foreign('loan_type_id')->references('loan_type_id')
//                ->on('loan_type_id')->onDelete('loan_type_id')->onUpdate('loan_type_id');
//                      $table->foreign('customer_id')->references('customer_id')
//                ->on('customer_id')->onDelete('customer_id')->onUpdate('customer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};