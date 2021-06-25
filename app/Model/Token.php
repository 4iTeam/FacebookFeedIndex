<?php
namespace App\Model;
use App\Model;
use App\Services\Google\GSuiteService;
use App\Services\Microsoft\Office365;
use App\Services\Token\RefreshTokenService;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Model\Fixed\Provider;
/**
 * Class Token
 * @package App\Model
 * @property $id
 * @property $uid
 * @property string $name
 * @property string $email
 * @property string $token
 * @property string $access_token
 * @property string $refresh
 * @property string $refresh_token
 * @property $provider
 * @property $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $expired_at
 * @property int $created
 * @property int expires_in
 * @property int $user_id
 * @property User $user
 */
class Token extends Model{

	const GOOGLE=Provider::GOOGLE;
	const MICROSOFT=Provider::MICROSOFT;
	const FACEBOOK=Provider::FACEBOOK;
	const LOGIN='login';
	protected $dates=[self::EXPIRED_AT];
	protected $table='tokens';
	protected $fillable = ['uid','user_id','name','email','token','refresh','meta','provider','type','expired_at'];


	use Model\Traits\MetaField;
	static function boot()
    {
        parent::boot();
        static::saving(function(Token $model){
            $model->name=str_limit($model->name);
        });
    }

    /**
     * @param array $attributes
     * @return mixed|static
     */
	public static function add($attributes){
		if(
			empty($attributes['uid']) ||
			empty($attributes['provider'])||
		    empty($attributes['token']) ||
		    empty($attributes['type'])

		){
			return new static();
		}

		$attributes['provider']=Provider::id($attributes['provider']);
        if(empty($attributes['email'])){
            $attributes['email']=$attributes['uid'];
        }

		return static::updateOrCreate(array_only($attributes,['uid','provider','type']),$attributes);


	}

    /**
     * @param $uid
     * @param string $provider
     * @param string $type
     * @return mixed
     */
    public static function findByUid($uid,$provider='fb',$type='login'){
        return static::where(compact('uid','provider','type'))->first();
    }
	public static function cleanup(){
	    return static::where('expired_at','<',Carbon::now())->delete();
    }
	function isProvider($provider){
		$provider=Provider::id($provider);
		return $provider==$this->provider;
	}
	function isType($type){
		return $type==$this->type;
	}
	function isGoogle(){
		return $this->isProvider(Token::GOOGLE);
	}
	function isMicrosoft(){
		return $this->isProvider(Token::MICROSOFT);
	}

	function getIcon(){
		return Provider::getIcon($this->provider);
	}
	function theProvider(){
		return Provider::render($this->provider);
	}


	function isExpired(){
		if(!$this->expired_at){
			return false;
		}
		$now=Carbon::now()->addSeconds(30);
		return $this->expired_at->lt($now);
	}


	function refresh(){
		return app()->make(RefreshTokenService::class)->refresh($this);
	}
	function refreshIfExpired(){
		if($this->isExpired()){
			$this->refresh();
		}
	}

	function __toString() {
		return $this->token;
	}

	protected function getCreatedAttribute(){
		return $this->updated_at->getTimestamp();
	}
	protected function getExpiresInAttribute(){
		return $this->expired_at->diffInSeconds($this->updated_at);
	}
	protected function getAccessTokenAttribute(){
		return $this->token;
	}
	protected function getRefreshTokenAttribute(){
		return $this->refresh;
	}
	protected function getFullnameAttribute(){
	    return $this->name.'-'.$this->uid.'-'.$this->type;
    }
	function user(){
	    return $this->belongsTo(User::class);
    }
}