<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 17-Mar-18
 * Time: 10:42 PM
 */

namespace App\Model\Facebook;


use App\Model;
use App\Services\Utils\Hashtag;
use Illuminate\Support\Carbon;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphNode;
use Illuminate\Support\Collection;

/**
 * Class Post
 * @package App\Model\Facebook
 * @property User $user
 * @property Collection $comments
 * @property $comment_count
 * @property $reaction_count
 * @property $share_count
 */
class Post extends Model
{
    protected $table='fb_posts';
    public $incrementing=false;
    public $timestamps=false;
    protected $dates=['created_at', 'updated_at'];
    protected $fillable=[
        'id','story','message','url','picture','feed_id','user_id','type',
        'comment_count','reaction_count','share_count','like_count','love_count','haha_count','wow_count','sad_count','angry_count',
        'created_at','updated_at',
    ];
    use Model\Traits\MetaField;

    public static function addFromGraph(GraphNode $post,$backTime=0){
        if(empty($post['message'])){
            return false;
        }
        $data=static::prepareData($post,$backTime);
        $post = static::firstOrCreate(['id'=>$data['id']],$data);
        $post->updateTags();
        return $post;
    }
    function hasUpdated($post){
        $data=static::prepareData($post);
        unset($data['id']);//don't need update id
        $this->fill($data);
        $this->setCounts($post);
        $this->save();
        $this->updateTags();
    }

    static function prepareData(GraphNode $post,$backTime=0){
        $ids=explode('_',$post['id']);
        if(count($ids)!=2){
            return false;
        }
        $feed_id=$ids[0];
        $post_id=$ids[1];
        $data=[];
        $data['id']=$post['id'];
        $data['story']=isset($post['story'])?$post['story']:'No story';
        $data['message']=$post['message'];
        $data['picture']=isset($post['full_picture'])?$post['full_picture']:null;
        $data['feed_id']=$feed_id;
        $data['user_id']=isset($post['from']['id'])?$post['from']['id']:'';
        User::addFromGraph($post['from']);
        $data['url']=$post['permalink_url'];
        $data['created_at']=$post['created_time'];
        $data['updated_at']=$post['updated_time'];
        if($backTime){
			if(isset($post['created_time']) && $post['created_time'] instanceof \DateTime){
				$data['created_at']=Carbon::instance($post['created_time'])->subSeconds($backTime);
			}else{
				$data['created_at']=Carbon::now()->subSeconds($backTime);
			}
			if(isset($post['updated_time']) && $post['updated_time'] instanceof \DateTime){
				$data['updated_at']=Carbon::instance($post['updated_time'])->subSeconds($backTime);
			}else{
				$data['updated_at']=Carbon::now()->subSeconds($backTime);
			}
		}else{
			$data['created_at']=$post['created_time']??Carbon::now();
			$data['updated_at']=$post['updated_time']??Carbon::now();
		}
        $data['type']=$post['type'];
        if(isset($post['reactions']) && $post['reactions'] instanceof GraphEdge){
            $data['reaction_count']=$post['reactions']->getTotalCount();
        }
        if(isset($post['comments']) && $post['comments'] instanceof GraphEdge){
            $data['comment_count']=$post['comments']->getTotalCount();
        }

        if(isset($post['shares'], $post['shares']['count'])){
            $data['share_count']=$post['shares']['count'];
        }
        return $data;
    }

    function updatedInSeconds($post){
        if(isset($post['updated_time']) && $post['updated_time'] instanceof \DateTime) {
            $updated = Carbon::instance($post['updated_time']);
            return $this->updated_at->diffInSeconds($updated,false);
        }
        return 1;
    }
    function updateTags(){
        $hashtags=Hashtag::parse($this->message);
        $tags=Tag::add($hashtags);
        $ids=$tags->pluck('id');
        $this->tags()->sync($ids);
        $tags->each(function(Tag $tag){
            $tag->updatePostCount();
        });
        return $this;
    }

    function setCounts($post){
        $this->setCommentCount($post);
        return $this;
    }
    function setCommentCount($post){
        $commentCountFromPost=null;
        if(isset($post['comments']) && $post['comments'] instanceof GraphEdge){
            $commentCountFromPost = $post['comments']->getTotalCount();
        }
        if($commentCountFromPost !==null) {
            $this->comment_count = $commentCountFromPost;
        }else{
            $this->comment_count = $this->comments()->count();
        }
        return $this;
    }
    function user(){
        return $this->belongsTo(User::class);
    }
    function tags(){
        return $this->belongsToMany(Tag::class,'fb_post_tag');
    }
    function comments(){
        return $this->hasMany(Comment::class);
    }
    function isLink(){
        return $this->type=='link';
    }
    protected function getContentAttribute(){
        $content=$this->message;
        $content=htmlspecialchars($content,ENT_QUOTES,'UTF-8',true);
        $content=nl2br($content);
        $content=Tag::link($content);
        $content=autolink($content,['rel'=>'nofollow noopener noreferrer','target'=>'_blank']);
        return $content;
    }
}
