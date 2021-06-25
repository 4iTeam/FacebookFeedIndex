<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 17-Mar-18
 * Time: 10:41 PM
 */

namespace App\Model\Facebook;


use App\Model;

/**
 * Class User
 * @package App\Model\Facebook
 * @property $id
 */
class User extends Feed
{
    public $incrementing=false;
    function __construct(array $attributes=[]) {
        parent::__construct($attributes);
        $this->setAttribute('type','user');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('user',function($query){
            $query->where('type','user');
        });
    }

	/**
	 * @param $slug
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null|static
	 */
    public static function findBySlug($slug){
    	return static::query()->where('slug',$slug)->first();
	}

    /**
     * @param $user
     * @return bool|static
     */
    static function addFromGraph($user){
        if(empty($user['name']) || empty($user['id'])){
            return false;
        }
        return static::firstOrCreate(['id'=>$user['id']],['name'=>str_limit($user['name']),'id'=>$user['id']]);
    }
    function updateCount(){
        $this->post_count=$this->posts()->count();
        $this->comment_count=$this->comments()->count();
        $this->save();
        return $this;
    }
    function comments(){
        return $this->hasMany(Comment::class);
    }
    public function getGraphAvatar($args=[]){
		$args=array_merge(['type'=>'normal'],$args);
		$src='https://graph.facebook.com/%s/picture';
		$src=sprintf($src,$this->id);
		$src=add_query_arg($args,$src);
		return $src;
	}
    function getAvatar($args=[]){
        $args=array_merge(['type'=>'normal'],$args);
        //$src='https://graph.facebook.com/%s/picture';
        //$src=sprintf($src,$this->id);
		$src=route('member.avatar',[$this->slug]);
        $src=add_query_arg($args,$src);
        return $src;
    }
    protected function getAvatarAttribute(){
        return $this->getAvatar();
    }
    protected function getAvatarLgAttribute(){
        return $this->getAvatar(['type'=>'large']);
    }



}
