<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataGrid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datagrid', function (Blueprint $table) {
            $table->increments('datagridid');
            $table->unsignedInteger('ownerid')->nullable();
            $table->string('table')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();

            $table->index('ownerid');

            $table->foreign('ownerid')
                ->references('userid')->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datadisplaysystem');
    }
}
