<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 08-Apr-18
 * Time: 7:00 PM
 */

namespace App\Console\Commands;

use App\Model\Facebook\Comment;
use App\Model\Facebook\Feed;
use App\Model\Facebook\Post;
use App\Model\Facebook\PostTag;
use App\Model\Facebook\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Archive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive {action}';

    function handle(){
        $action=$this->argument('action');
        $this->line($action);
        switch ($action){
            case 'clean':
                $this->clean();
                break;
			case 'feed_slug_db':
				$this->doUpgradeForFeedSlug();
				break;
			case 'feed_slug_generate':
				$this->doGenerateForFeedSlug();
				break;
        }
    }
    function doGenerateForFeedSlug(){
    	Feed::query()->whereNull('slug')->each(function(Feed $feed){
    		$this->line('Updating: '.$feed->id);
			$feed->slug=(string)Str::uuid();
			$feed->save();
		});
    	$this->line('Done');
	}
    function doUpgradeForFeedSlug(){
    	Schema::table('fb_feeds',function (Blueprint $table){
    		$table->unique('slug');//add unique to slug
		});
	}
    function clean(){
        Feed::query()->truncate();
        Post::query()->truncate();
        Tag::query()->truncate();
        Comment::query()->truncate();
        PostTag::query()->truncate();
        $this->info('Done');
    }

}
