<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 02-Apr-18
 * Time: 12:15 PM
 */

namespace App\Model\Traits\User;
use App\Model\Token;
use Illuminate\Support\Collection;

/**
 * Trait UserFacebookTrait
 * @package App\Model\Traits\User
 * @property Token $facebook_token
 * @property Token $facebook
 * @property $facebook_id
 * @property Collection $facebookTokens
 */
trait UserFacebookTrait
{
    /**
     * @param $facebook_id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findByFacebook($facebook_id){
        $email=$facebook_id.'@'.'facebook.com';
        return static::findByEmail($email);
    }
    /**
     * @return \Illuminate\Database\Query\Builder
     */
    function facebookTokens(){
        return $this->tokens()->where('provider',Token::FACEBOOK);
    }
    protected function getFacebookAttribute(){
        return $this->remember('facebook',function(){
            return $this->facebookTokens()->where('type',Token::LOGIN)->first();
        });
    }
    protected function getFacebookIdAttribute(){
        if(strpos($this->email,'@facebook.com')!==false) {
            return str_replace('@facebook.com', '', $this->email);
        }
        if($this->facebook){
            return $this->facebook->uid;
        }
        return false;
    }
    protected function getFacebookTokenAttribute(){
        return $this->remember('facebook_token',function(){
            return $this->facebookTokens()->first();
        });
    }
}