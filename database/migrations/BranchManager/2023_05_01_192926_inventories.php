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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->integer('branch_id');
            $table->integer('user_id');
            $table->text('inventory_name');
            $table->integer('inventory_number');
            $table->float('inventory_price');
            $table->text('inventory_desc');
            $table->string('serial_no');
            $table->string('inventory_status');
            $table->date('DOR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
