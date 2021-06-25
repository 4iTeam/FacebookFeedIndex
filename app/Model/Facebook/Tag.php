<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 17-Mar-18
 * Time: 10:42 PM
 */

namespace App\Model\Facebook;


use App\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $table='fb_tags';
    protected $fillable=['tag','slug','post_count'];
    static function add($hashtags){
        if(is_array($hashtags)) {
            $tags = collect();
            foreach ($hashtags as $tag) {
                $tags[] = Tag::firstOrCreate(['tag' => $tag], ['tag' => $tag, 'slug' => str_slug($tag)]);
            }
            return $tags;
        }
        return Tag::firstOrCreate(['tag' => $hashtags], ['tag' => $hashtags, 'slug' => str_slug($hashtags)]);
    }
    function posts(){
        return $this->belongsToMany(Post::class,'fb_post_tag');
    }
    function updatePostCount(){
        $this->post_count=$this->posts()->count();
        $this->save();
    }
    static function link($content){
        $tagUrl=url('tag');
        $content = preg_replace_callback('/([^\w])#(\w*?[a-zA-Z]+\w*)|^#(\w*?[a-zA-Z]+\w*)/u', function($matches)use($tagUrl){
            if(isset($matches[3])){
                $matches[2]=$matches[3];
            }
            $href = $tagUrl . '/' . Str::lower($matches[2]);
            $text = '#' . $matches[2];
            return sprintf('%s<a href="%s"><span class="badge badge-indigo">%s</span></a>',$matches[1],$href,$text);
        },$content);
        return $content;
    }
}