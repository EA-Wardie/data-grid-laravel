<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDatagridTableChangeConfigurationTypeToText extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('datagrid', function (Blueprint $table) {
            $table->text('configuration')->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('datagrid', function (Blueprint $table) {
            $table->json('configuration')->change();
        });
    }
}
