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
        Schema::create('agreement_forms', function (Blueprint $table) {
            $table->id('agreement_form_id');
            $table->string('amount');

           $table->unsignedBigInteger('customer_id');
//            $table->foreign('customer_id')->references('customer_id')
//                ->on('customer_id')->onDelete('customer_id')->onUpdate('customer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_forms');
    }
};