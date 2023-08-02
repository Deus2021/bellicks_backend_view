<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('guarantor_name')->nullable()->change();
            $table->string('guarantor_phone')->nullable()->change();
            $table->string('customer_guarantee')->nullable()->change();
            $table->string('guarantor_nida')->nullable()->change();
            $table->string('guarantor_gender')->nullable()->change();
            $table->string('guarantor_photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            
        });
    }
};