<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('domain',191)->unique();
            $table->string('name');//branch
            $table->unsignedBigInteger('owner_id')->default(0)->index();//owner
            $table->unsignedBigInteger('parent_id')->default(0)->index();//for alias
            $table->longText('meta')->nullable();
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
        Schema::dropIfExists('sites');
    }
}
