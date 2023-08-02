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
        Schema::table('loan_types', function (Blueprint $table) {
            $table->string("desc");
            $table->string("insurance");
            $table->string("duration");
            $table->string("rate");
            $table->string("fixed_penalty");
            $table->string("penalty_percentage");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            $table->dropIfExists('desc');
            $table->dropIfExists('insurance');
            $table->dropIfExists('duration');
            $table->dropIfExists('rate');
            $table->dropIfExists('fixed_penalty');
            $table->dropIfExists('penalty_percentage');
        });
    }
};
