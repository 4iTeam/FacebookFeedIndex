<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFbFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_feeds', function (Blueprint $table) {
            $table->string('id',100)->primary();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('type',10)->index();
            $table->unsignedInteger('post_count')->default(0);
            $table->unsignedInteger('reaction_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedInteger('member_count')->default(0);
            $table->boolean('index')->default(false);
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
        Schema::dropIfExists('fb_feeds');
    }
}
