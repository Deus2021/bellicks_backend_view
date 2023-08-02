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
        Schema::create('users', function (Blueprint $table) {
            $table->id("user_id");
            $table->string('profile_image');
            $table->string('id_img');
            $table->integer('id_number');
            $table->string('id_type');
            $table->string('DOB');
            $table->string('employement_date');
            $table->string('salary');
            $table->unsignedBigInteger('access_id');
            $table->unsignedBigInteger('role_id');
            // $table->foreign('role_id')->references('role_id')->on('roles');
            $table->unsignedBigInteger('branch_id');
            // $table->foreign('branch_id')->references('branch_id')->on('branches');
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};