<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('tag',20)->index()->default('default');
            $table->string('command');
            $table->string('desc')->nullable();
            $table->text('output')->nullable();
            $table->string('cron')->default('');
            $table->integer('result')->nullable();
            $table->unsignedInteger('retry')->default(0);
            $table->unsignedInteger('max_retry')->default(5);
            $table->enum('status',['new','ready','running','finished','stopped'])->default('new');
            $table->string('request')->default('');//on finish
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
