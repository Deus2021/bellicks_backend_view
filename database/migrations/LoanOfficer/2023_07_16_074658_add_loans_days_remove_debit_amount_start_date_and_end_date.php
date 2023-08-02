<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('debit_amount');
            $table->string('loan_days');
            $table->date('start_date')->default(now());
            $table->date('end_date')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->string('loan_days')->change();
            $table->string('debit_amount')->after('loan_days');
        });
    }
};