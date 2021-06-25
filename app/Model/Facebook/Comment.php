<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 17-Mar-18
 * Time: 10:42 PM
 */

namespace App\Model\Facebook;


use App\Model;
use Carbon\Carbon;

class Comment extends Model
{
    protected $table='fb_comments';
    public $incrementing=false;
    protected $fillable=['id','comment','created_at','updated_at','user_id','post_id'];
    static function addFromGraph($comment,$post){
        $data=[];
        $data['id']=$comment['id'];
        $data['comment']=isset($comment['message'])?$comment['message']:null;
        $data['created_at']=$comment['created_time'];
        $data['updated_at']=isset($comment['updated_time'])?$comment['updated_time']:null;
        $data['user_id']=$comment['from']['id'];
        $data['post_id']=$post['id'];
        User::addFromGraph($comment['from']);
        $comment = static::updateOrCreate(array_only($data,'id'),$data);
        return $comment;
    }

    public static function shouldUpdate($comment){
        if(isset($comment['id'])){
            if($comment_obj=static::find($comment['id'])){
                //found a comment
                if(isset($comment['updated_time']) && $comment['updated_time'] instanceof \DateTime) {
                    $updated = Carbon::instance($comment['updated_time']);
                    if ($comment_obj->updated_at_gmt==$updated) {
                        return false;
                    }
                }
                if(isset($comment['created_time']) && $comment['created_time'] instanceof \DateTime) {
                    $created_at = Carbon::instance($comment['created_time']);
                    if ($comment_obj->created_at_gmt==$created_at) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function user(){
        return $this->belongsTo(User::class);
    }
}