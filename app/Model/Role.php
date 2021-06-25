<?php
namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App\Model
 * @property $id
 * @property string $name
 * @property array $caps
 * @property array $caps_for_edit
 * @property Collection $users
 * @method static static create($attr)
 * @method static static find($id)
 *
 */
class Role extends Model {
	protected $fillable=['name','caps'];
	protected $appends=['caps_for_edit'];
	static $special_caps=['_all_caps'];
	static $protected=['admin','user','customer'];
	public $timestamps=false;
	protected $caps_cache;

	function hasAllCaps(){
		return !empty($this->caps['_all_caps']);
	}
	function getCaps(){
		return array_keys($this->caps);
	}
	function setCapsAttribute($value){
		if(is_array($value)){
			$value=array_map(function($v){
				return str_slug($v,'_');
			},$value);
		}else{
			$value=[str_slug($value,'_')];
		}
		$value=array_map(function($v){
		    if(!in_array($v,static::$special_caps)){
		        $v=trim($v,'_');
            }
            return $v;
        },$value);
		$this->caps_cache=$value;
		$this->attributes['caps']=serialize($value);
	}
	protected function getCapsAttribute($value){
	    if(is_null($this->caps_cache)) {
            $this->caps_cache=unserialize($value);
        }
        return $this->caps_cache;
	}
	protected function getCapsForEditAttribute($value){
        $caps=$this->caps;
        $caps=array_diff_key($caps,array_flip(static::$special_caps));
        return $caps;
    }

	function users(){
		return $this->hasMany(User::class);
	}

	/**
	 * @param $name
	 *
	 * @return static
	 */
	public static function findByName($name){
		return static::where('name',$name)->first();
	}
}