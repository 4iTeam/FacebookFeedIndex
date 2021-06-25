<?php
namespace App\Model\Traits;
use Closure;
trait SingletonTrait{
	protected $_singletons=[];
	protected static $_ssingletons=[];
	protected function remember($name,$value=null,$register=false){
		if(!array_key_exists($name,$this->_singletons)||$register){
			if($value instanceof Closure){
				$value=$value();
			}
			$this->_singletons[$name]=$value;
		}
		return $this->_singletons[$name];
	}
	protected static function staticRemember($name,$value=null,$register=false){
        if (! isset(static::$_ssingletons[static::class])) {
            static::$_ssingletons[static::class] = [];
        }
		if(!array_key_exists($name,static::$_ssingletons[static::class])||$register){
			if($value instanceof Closure){
				$value=$value();
			}
            static::$_ssingletons[static::class][$name]=$value;
		}
		return static::$_ssingletons[static::class][$name];
	}
}