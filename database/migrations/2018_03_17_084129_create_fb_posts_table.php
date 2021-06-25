<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFbPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_posts', function (Blueprint $table) {
            $table->string('id',100)->primary();
            $table->string('feed_id',100)->index();
            $table->string('user_id',100)->index();
            $table->string('story');
            $table->text('message');
            $table->string('url');
            $table->string('picture',1000)->nullable();
            $table->string('type',20)->nullable();
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('reaction_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('love_count')->default(0);
            $table->unsignedInteger('haha_count')->default(0);
            $table->unsignedInteger('wow_count')->default(0);
            $table->unsignedInteger('sad_count')->default(0);
            $table->unsignedInteger('angry_count')->default(0);
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
        Schema::dropIfExists('fb_posts');
    }
}
