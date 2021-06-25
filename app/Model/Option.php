<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Option extends Model {
    protected $table='options';
    protected $fillable = ['name','value','autoload'];
    public $timestamps = false;
    public static $checkOptionName=true;
    protected static function boot() {
        static::saving(function(Option $model){
            if(static::$checkOptionName){
                if(!$model->exists  || //creating new or
                    ($model->exists&&$model->getOriginal('name')!=$model->name) //change name of existing option
                ){
                    if(Option::where('name',$model->name)->first()){//an option with same name exists
                        return false;
                    }
                }
            }
            if(!$model->getAttribute('autoload')){
                $model->setAttribute('autoload','yes');
            }

        });
        parent::boot();
    }
    function getValueAttribute($value){
        return maybe_unserialize($value);
    }
    function setValueAttribute($value){
    	if($value===null){
    		$value='';
	    }
        $this->attributes['value'] = maybe_serialize($value);
    }
}