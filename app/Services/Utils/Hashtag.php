<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 02-Feb-18
 * Time: 2:12 PM
 */

namespace App\Services\Utils;


use Illuminate\Support\Str;

class Hashtag
{
    static $cache=[];
    function isValid($message,$prefix='4it_'){
        $hashTags=$this->parse($message);
        foreach ($hashTags as $hashTag){
            if(strpos($hashTag,$prefix)==0){
                return true;
            }
        }
        return empty($hashTags)?0:false;
    }

    /**
     * @param $message
     * @return array|mixed
     */
    static function parse($message){
        if(!$message){
            return [];
        }
        $key=md5($message);
        if(!isset(static::$cache[$key])) {
            static::$cache[$key]=[];
            if (preg_match_all('/([^\w])#(\w*?[a-zA-Z]+\w*)|^#(\w*?[a-zA-Z]+\w*)/u', $message, $matches)) {
                static::$cache[$key]=array_map(function ($value) {
                    return trim(Str::lower($value));
                }, array_replace($matches[2],array_filter($matches[3])));
            }
        }
        return static::$cache[$key];
    }
    function contains($str,$check){
        $allTags=static::parse($str);
        foreach (array_wrap($check) as $tag){
            if(in_array($tag,$allTags)){
                return true;
            }
        }
        return false;
    }
}