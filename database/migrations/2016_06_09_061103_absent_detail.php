<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AbsentDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sd')->create('tbl_absent_detail', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->integer('ansar_id');
            $table->integer('month');
            $table->integer('year');
            $table->integer('kpi_id');
            $table->integer('total_absent');
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
        Schema::connection('sd')->drop('tbl_absent_detail');
    }
}
