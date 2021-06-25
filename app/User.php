<?php

namespace App;
use App\Model\Token;
use App\Model\Role;
use App\Model\Traits\RolePermissionTrait;
use App\Model\Traits\SingletonTrait;
use App\Model\Traits\TimeZoneTrait;
use App\Model\Traits\User\UserAvatarTrait;
use App\Model\Traits\User\UserFacebookTrait;
use App\Model\Traits\User\UserStatus;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\Traits\User\UserMetaTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Class User
 * @package App
 * @property $id
 * @property $name
 * @property $email
 * @property $password
 * @property $role_id int
 * @property Role $role
 * @property $status
 * @method static User|null find($id)
 * @method static User|null create($attr)
 * @method static User|null findOrFail($id)
 *
 * @property $avatar
 */
class User extends Authenticatable
{
    protected $dates=['expired_at'];
    use TimeZoneTrait,Notifiable,
        SingletonTrait,
        RolePermissionTrait,
        UserStatus,
        UserMetaTrait,
        UserAvatarTrait,
        UserFacebookTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','status','role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	/**
	 * @param $data
	 * @param $skip
	 * @return \Illuminate\Validation\Validator
	 */
	public static function getValidator($data,$only=null,$skip=null){
		$validator= Validator::make($data, static::getValidateRules($only,$skip)->toArray());
		return $validator;
	}

	/**
	 * @param null $only
	 * @param null $skip
	 * @return Collection
	 */
	public static function getValidateRules($only=null,$skip=null){
		$status=static::allStatuses()->pluck('status')->all();
		$status=join(',',$status);
		$rules= [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|min:6|confirmed',
			'status'=>'required|in:'.$status,
			'role_id' => 'required|exists:roles,id'
		];
		$the_rules= new Collection($rules);
		if($only){
			return $the_rules->only($only);
		}
		if($skip){
			return $the_rules->except($skip);
		}
		return $the_rules;
	}

	/**
	 * @param $email
	 *
	 * @return static|null
	 */
	public static function findByEmail($email){
		if(empty($email)){
			return null;
		}
		$email=Str::lower($email);
		return static::where('email',$email)->first();
	}



	/**
	 * @param $id
	 *
	 * @return static|null
	 */
	public static function findCache($id){
		return static::staticRemember('find:'.$id,static::find($id));
	}

    /********************
     * RELATIONS
     *******************/
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function tokens(){
        return $this->hasMany(Token::class);
    }


    /***************************************
     * ATTRIBUTES
     ************************************/
    /**
     * @return Collection
     */





}
