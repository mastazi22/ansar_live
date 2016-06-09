<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DemandLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_demand_log',function(Blueprint $table){
            $table->increments('id');
            $table->integer('kpi_id');
            $table->longText('sheet_name');
            $table->string('memorandum_no',255);
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
        Schema::drop('tbl_demand_log');
    }
}
