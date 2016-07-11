<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KpiPaymentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection('sd')->create('tbl_kpi_payment_log',function(Blueprint $table){
            $table->increments('id');
            $table->integer('kpi_id');
            $table->integer('amount');
            $table->dateTime('payment_date');
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
        Schema::connection('sd')->drop('tbl_kpi_payment_log');
    }
}
