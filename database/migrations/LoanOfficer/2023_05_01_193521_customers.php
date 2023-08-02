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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('customer_img');
            $table->string('customer_img_id');
            $table->string('nida_number');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_gender');
            $table->string('customer_phone');
            $table->string('customer_relation');
            $table->string('customer_dob');
            $table->string('customer_guarantee');
            $table->string('customer_residence');

            $table->string('guarantor_photo');
            $table->string('guarantor_name');
            $table->string('guarantor_phone');
            $table->string('guarantor_gender');

            $table->timestamps();
        });
    }
    // $table->string('customer_location');
    // $table->string('guarantor_location');
    // $table->string('guarantor_relation');
    //  $table->string('guarantor_number');
    //  $table->string('guarantor_photo_id');

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
