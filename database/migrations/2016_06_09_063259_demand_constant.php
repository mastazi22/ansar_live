<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DemandConstant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sd')->create('tbl_demand_constant', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('cons_name',255);
            $table->integer('cons_value');
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
        Schema::connection('sd')->drop('tbl_demand_constant');
    }
}
