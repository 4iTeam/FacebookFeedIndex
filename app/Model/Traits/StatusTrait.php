<?php
namespace App\Model\Traits;
use App\Exceptions\InvalidStatusException;
use App\User;
use Illuminate\Support\Collection;

trait StatusTrait{
    protected static $_statuses=[];
	protected static $__skipStatusCheck=false;
    static function registerStatus($status,$args=[]){
        if(is_array($status)){
            foreach ($status as $name=>$_args){
                self::registerStatus($name,$_args);
            }
        }else {
            if(!$args){
                $args=$status;
            }
            if (!is_array($args)) {
                $args=['label'=>$args];
            }
            if(!isset($args['label'])){
                $args['label']=$status;
            }
            $args['status'] = $status;
            static::$_statuses[$status] = $args;
        }
    }
    /**
     * @return Collection
     */
    static function allStatuses(){
        return new Collection(static::$_statuses);
    }
    static function statusExists($status){
	    if(static::$__skipStatusCheck){
		    if(is_int(static::$__skipStatusCheck)){
			    static::$__skipStatusCheck--;
		    }
		    return true;
	    }
        return isset(static::$_statuses[$status])?true:false;
    }
    static function bootStatusTrait(){
        static::saving(function($model){
            if(empty($model->status) && property_exists($model,'_defaultStatus') ){

                $model->status=static::$_defaultStatus;
            }
            if(!static::statusExists($model->status)){
                throw (new InvalidStatusException())->setModel($model);
            }

        });
    }
    function setStatus($status,$save=false){
        if(self::statusExists($status))
            $this->status=$status;
        $save&&$this->save();
        return $this;
    }
    function updateStatus($status){
		return $this->setStatus($status,true);
    }

}