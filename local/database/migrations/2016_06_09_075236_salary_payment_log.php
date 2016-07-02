<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalaryPaymentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection('sd')->create('tbl_salary_payment_log',function(Blueprint $table){
            $table->increments('id');
            $table->longText('sheet_name');
            $table->dateTime('generated_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::connection('sd')->drop('tbl_salary_payment_log');
    }
}
