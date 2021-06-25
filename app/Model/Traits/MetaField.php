<?php
namespace App\Model\Traits;
use Illuminate\Support\Str;

/**
 * Class MetaField
 * @package App\Model\Traits
 * @property $meta
 * @property $attributes
 * @property array $fillable_meta
 * @property array $append_meta
 */
trait MetaField{
	private $_meta;
	/**
	 * get or update Meta data
	 * @param null $key
	 *
	 * @return bool|string
	 */
	function meta($key=null){
		if(func_num_args()<=1) {
			if (is_array($key)) {
				$this->setMeta($key);
				return $this->save();
			}else{
				if(is_null($key)){
					return $this->meta;
				}
				return isset($this->meta[$key])?$this->meta[$key]:null;
			}
		}else{
			$value=func_get_arg(1);
			$this->setMeta($key,$value);
			return $this->save();
		}

	}
	function setMeta($key,$value=null){
		$meta=$this->meta;
		if(!is_array($key)){
			$key=[$key=>$value];
		}
		foreach ($key as $meta_key=>$meta_value){
			if($this->_removeMeta($meta_key,$meta_value)){//check if we should remove meta
				unset($meta[$meta_key]);
			}else {
				$meta[$meta_key] = $meta_value;
			}
		}
		$this->meta=$meta;
		return $this;
	}
	function updateMeta(array $meta){
		$this->meta=$meta;
		return $this->save();
	}
	protected function _removeMeta($key,$value){
	    return is_null($value);
    }

	function getAttribute( $name ) {
		$value=parent::getAttribute($name);
		if(is_null($value)){
			$value = $this->meta($name);
            if($this->hasGetMutatorMeta($name)){
                return $this->mutateAttributeMeta($name,$value);
            }
		}
		return $value;
	}
    function setAttribute($name, $value)
    {
        if ($this->hasSetMutatorMeta($name)) {
            $method = 'set'.Str::studly($name).'AttributeMeta';
            return $this->{$method}($value);
        }elseif(property_exists($this,'fillable_meta')) {
            if (in_array($name, $this->fillable_meta)){
                return $this->setMeta($name,$value);
            }
        }
        return parent::setAttribute($name,$value);
    }
    /**
     * Get the value of an meta attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttributeMeta($key, $value)
    {
        return $this->{'get'.Str::studly($key).'AttributeMeta'}($value);
    }
    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutatorMeta($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'AttributeMeta');
    }

    public function hasSetMutatorMeta($key)
    {
        return method_exists($this, 'set'.Str::studly($key).'AttributeMeta');
    }


    function fill(array $attributes){
        $this->fillMeta($attributes);
        unset($attributes['meta']);
        return parent::fill($attributes);
    }

    function fillMeta(&$attributes){
        if(property_exists($this,'fillable_meta')) {
            $meta = array_intersect_key($attributes, array_flip($this->fillable_meta));
            foreach($meta as $key=>$value) {
                $this->$key=$value;
            }
        }
        return $this;
    }
    function toArray()
    {
        return array_merge(parent::toArray(),$this->metaToArray());
    }
    function metaToArray(){
        $array=[];
        if(property_exists($this,'fillable_meta')) {
            $meta=[];
            foreach($this->fillable_meta as $key) {
                $meta[$key] = $this->$key;
            }
            $array=array_merge($array,$meta);
        }
        if(property_exists($this,'append_meta')){
            $meta=[];
            foreach($this->append_meta as $key) {
                $meta[$key] = $this->$key;
            }
            $array=array_merge($array,$meta);
        }
        return $array;
    }
    protected function getMetaAttribute($value){
        if(!isset($this->_meta)){
            @$this->_meta=unserialize($value);
            if(!is_array($this->_meta)){
                $this->_meta=array();
            }
        }
        return $this->_meta;
    }
    protected function setMetaAttribute($value){
        if(!is_array($value)){
            return ;
        }
        if($this->getFillable()) {//remove primary field from meta
            $value = array_diff_key( $value, array_flip( $this->getFillable() ) );
        }
        $this->_meta=$value;
        $this->attributes['meta'] = serialize($value);
    }

}