<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('tokens',function(Blueprint $table){
		    $table->bigIncrements('id');
		    $table->unsignedBigInteger('user_id')->nullable();
		    $table->string('uid',191)->index();
		    $table->string('name');
		    $table->string('email',191)->index();
		    $table->text('token');
		    $table->text('refresh')->nullable();
		    $table->longText('meta')->nullable();
		    $table->string('provider',20)->index();//facebook/google
		    $table->string('type',20)->default('login')->index();//login
		    $table->timestamps();
		    $table->dateTime('expired_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists('tokens');
    }
}
