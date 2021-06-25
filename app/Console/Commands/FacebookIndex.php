<?php

namespace App\Console\Commands;

use App\Model\Facebook\Comment;
use App\Model\Facebook\Post;
use App\Model\Facebook\User;
use App\Services\Facebook\FacebookService;
use App\Services\Utils\Hashtag;
use Exception;
use Facebook\Exceptions\FacebookAuthenticationException;
use Facebook\Exceptions\FacebookResponseException;
use Illuminate\Support\Carbon;
use Facebook\GraphNodes\GraphEdge;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FacebookIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:index {mode=new} {type=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $group='1415192401896193';
    protected $mode='all';
    protected $type=['p','c'];
    protected $stopIfNoNewData=false;
    protected $facebookService;
    protected $args=[];
    protected $noOutput=false;
    protected $postLimit=100;
    protected $commentLimit=100;
    protected $hashTag;


    public function __construct(FacebookService $facebookService, Hashtag $hashTag)
    {
        $token=config('archive.token');
        $this->facebookService=$facebookService->withToken($token)->debug();
        $this->group=config('archive.group');
        $this->hashTag=$hashTag;
        parent::__construct();
    }
    protected function setType(){
        $type=$this->argument('type');
        if($type==='0'){
            $type='';
        }
        if(!$type){
            $type=config('archive.type');
        }
        if(!$type){
            $type='p,c';
        }
        if(strpos($type,',')) {
            $this->type = preg_split('/[\s,]+/', $type);
        }else {
            $this->type = str_split($type);
        }
        return $this;
    }

    public function handle()
    {
        $this->mode=$mode=$this->argument('mode');
        $this->setType();

        $this->line('Facebook Index tool. Run mode: '.$mode.':'.join(',',$this->type));

        if(!$this->group || !$this->facebookService->getAccessToken()){
            $this->error('No Group or access token configured');
            return ;
        }
		try {
			$me=$this->facebookService->me();
		}catch (FacebookResponseException $e){
        	$this->error($e->getMessage());
        	return ;
		}

        $this->info('Run with:'.$me['name'].'('.$me['id'].')');

        $this->args=[
            'limit'=>$this->postLimit,//500 as default for max speed
            'fields'=>'id,from,type,story,message,created_time,updated_time,permalink_url,full_picture,comments.fields(id).summary(1).limit(0),reactions.fields(id).summary(1).limit(0),shares'
        ];
        if($mode=='new') {
            if($since=$this->getSince()) {//new post today or since last post
                $this->line('Since:'.$since);
                $this->args['since'] = $since;

            }
        }

        if($mode=='service'){
            while(1){
				$this->line('start');
				$this->args['since'] = $this->getLastPostUpdated(Carbon::now()->subMinute(1));
				$this->fetch();
				$this->line('There will be no output anymore');
				$this->noOutput = true;
				try {
					sleep(random_int(20, 60));
				} catch (Exception $e) {
				}
			}
        }else {
            $this->fetch();
			$this->updateSince();
			$this->info('Completed');
        }

    }

    protected function updateSince($value=null){
    	$value=$value?:$this->getLastPostUpdated();
    	update_option('facebook:index:since',$value);
	}
    protected function getSince(){
		return get_option('facebook:index:since');
	}
    /**
     * @param Carbon|null $default
     * @return null|string
     */
    protected function getLastPostUpdated(Carbon $default=null){
        $lastPost=Post::query()->latest('updated_at')->first();
        $since=$default;
        if($lastPost){
            if($lastPost->updated_at_gmt instanceof Carbon) {
                $since = $lastPost->updated_at_gmt;
            }
        }
        if($since===null){
            return null;
        }
        return $since->toW3cString();
    }
    protected function fetch($args=[]){
        if(!$args){
            $args=$this->args;
        }
        if($this->isFetchPosts()) {
            $this->fetchPostsComments($args);
        }elseif($this->isFetchComments()){
            $this->fetchCommentsOnly($args);
        }
    }
    protected function fetchPostsComments($args){
        $this->facebookService->getAsync('/'.$this->group.'/feed',$args,function(GraphEdge $items){
            $this->line('Found '.count($items).' posts');
            $users_to_update=collect();
            foreach($items as $post){
                $this->line('post: '.$post['id']);
                if($this->isSkipPost($post)){
                    $this->info('skip');
                    continue;
                }

                $post_obj=Post::find($post['id']);

                if(!$post_obj){
                	$this->line('A new post');
					$postUpdatedInSeconds=3600;
                    $post_obj=Post::addFromGraph($post,$postUpdatedInSeconds);

                }else{
					$this->line('Existing post');
                    $postUpdatedInSeconds=$post_obj->updatedInSeconds($post);
                }
				if($postUpdatedInSeconds<=0){
					$this->line('Nothing new on this post');//Nothing new on latest post, so don't need to check older post
					continue ;
				}


                if($post_obj){
                    if($post_obj->user) {
                        $users_to_update[$post_obj->user->id] = $post_obj->user;
                    }
                }else{
                    $this->warn('No post added');
                }
                $hasCommentsCountFromPostData=false;
                $comments=$post['comments']??null;
				if($comments instanceof GraphEdge){
					$hasCommentsCountFromPostData=$comments->getTotalCount()!==null;
				}
				$reasonSkipComments='Invalid post';//default reason
				$fetchComments=$this->isFetchComments();
				if(!$fetchComments){
					$reasonSkipComments='Run mode';//run mode
				}
				if($this->isFetchCommentsCount() && $hasCommentsCountFromPostData){//we run in comment count mode but count already present in payload
					$fetchComments=false;
					$reasonSkipComments='We already got comment count';//we already have data
				}
                if($fetchComments && $post_obj && $post_obj->exists) {
                	$this->line("Fetching comments");
					$this->updateCommentForPost($post_obj,$postUpdatedInSeconds);
                }else{
                    $this->warn('Skip comments: '.$reasonSkipComments);
                }

                if($post_obj) {
                    $post_obj->hasUpdated($post);
                }
            }
            $this->line($users_to_update->count().' users to update');
            $users_to_update->each(function(User $user){
                $user->updateCount();
            });
            usleep(1000);
            return true;
        });
    }
    protected function updateCommentForPost(Post $post,$postUpdatedInSeconds){
		$commentsPerSecond=1;
		$commentLimit=min($postUpdatedInSeconds*$commentsPerSecond,$this->commentLimit);//assume we can have 1 comments per second and max 500
		if($commentLimit<=0){
			$this->warn('No comments for this post because no update on this post');
		}
		$cFields=['id','from','message','created_time','updated_time'];
		if($this->isFetchCommentsCount()){
			$cFields=['id','from','created_time','updated_time'];
		}
		$edge=$post['id'] . '/comments';
		$comments_was_updated=$post->meta('comments_updated');//check the last
		$post->meta('comments_updated',null);//set current
		$this->facebookService->getAsync($edge, ['limit' => $commentLimit, 'fields' => join(',',$cFields), 'order' => 'reverse_chronological'], function ($comments) use ($post,$comments_was_updated) {
			$this->line('Found: ' . count($comments) . ' comments');
			foreach ($comments as $comment) {
				if (!Comment::shouldUpdate($comment)) {

					if ($comments_was_updated) {//comment updated success before, then we stop if no new comments
						$this->line('No new comments');
						return false;
					}else{
						continue;
					}
				}

				if(empty($comment['from'])){
					$this->warn('Missing comment from');
					continue;
				}
				if ($comment_obj = Comment::addFromGraph($comment, $post)) {
					if ($comment_obj->user) {
						$users_to_update[$comment_obj->user->id] = $comment_obj->user;
					}
				}
			}
			sleep(3);
			return true;
		});
		$post->meta('comments_updated',true);
	}
    protected function fetchCommentsOnly($args){

    }
    protected function isFetchPosts(){
        return in_array('p',$this->type);
    }
    protected function isFetchComments(){
        return in_array('c',$this->type)||$this->isFetchCommentsCount();
    }
    protected function isFetchCommentsCount(){
        return in_array('cc',$this->type);
    }
    protected function isSkipPost($post){
        if(!isset($post['id'])){
            $this->warn('Post have no id');
            return true;
        }
        if(empty($post['from'])){
            $this->warn('Post does not have from field');
            return true;
        }
        if(empty($post['message'])){
            $this->warn('Empty post');
            return true;
        }
        if(Str::contains($post['message'],'4it_private')){
        	$this->warn("Private post");
        	return true;
		}
        $skips=['1415192401896193_1847925161956246'];
        if( in_array($post['id'],$skips)){
            $this->warn('Post in skip list');
            return true;
        }
        return false;

    }
    public function line($string, $style = null, $verbosity = null)
    {
        if($this->noOutput){
            return ;
        }
        parent::line($string, $style, $verbosity);
    }
}
