<?php
namespace App\Model\Traits;
use Illuminate\Support\Str;

/**
 * Trait MetaTrait
 * @package App\Model\Traits
 * @property array $meta
 */
trait MetaTrait{
    protected $_meta_caches;
    function load_meta($fields=[]){
        //Load meta
        if($fields) {
            if($fields=='*'){
                $this->load('meta_relation');
            }elseif(is_array($fields)) {
                $this->load(['meta_relation' => function ($q) use ($fields) {
                    $q->whereIn('meta_key', $fields);
                }]);
            }
        }
        $this->_update_meta_caches();
        return $this->_meta_caches;
    }
    static function bootUserMetaTrait(){
        static::deleting(function($metaable) { // before delete() method call this
            $metaable->meta_relation()->delete();
        });
    }
    protected function _update_meta_caches(){
        //update meta cache
        if($this->relationLoaded('meta_relation')) {
            foreach ($this->getRelation('meta_relation') as $meta) {
                $this->_meta_caches[$meta->meta_key] = $meta->meta_value;
            }
            return true;
        }
        return false;
    }
    protected function _init_meta_caches(){
        if(is_null($this->_meta_caches)){//first time access meta
            $this->_meta_caches=[];
            if(!$this->_update_meta_caches())
                $this->load_meta('*');
        }
    }
    /**
     * get Current user meta value
     * @internal  $key (optional)
     * @internal $value (optional)
     * @return mixed
     */
    public function meta(){
        $numArgs=func_num_args();
        if($numArgs==0){
        	return $this->meta;
        }
	    $key=func_get_arg(0);
        if($numArgs==1) {
            if(is_array($key)){
                return $this->updateMeta($key);
            }
            if (isset($this->meta[$key])) {
                return $this->meta[$key];
            }
            return null;
        }else{
            $value=func_get_arg(1);
            return $this->updateMeta($key,$value);
        }
    }
    public function updateMeta($key,$value=''){
        $this->_init_meta_caches();
        if(is_array($key)){
            foreach ($key as $_key=>$_val){
                $this->updateMeta($_key,$_val);
            }
            return true;
        }else {

            $meta = $this->meta_relation()->where(['meta_key'=>$key])->first();
            //retrieve old meta, so we can use setMetaValueAttribute
            //need to refactor this we will save one query here
            if ($meta) {
                if(is_null($value)){
                    $this->deleteMeta($key);
                }else {
                    $meta->meta_value = $value;
                    $meta->save();
                }
            } else {
                if(!is_null($value))
                    $meta=$this->meta_relation()->create(['meta_key' => $key, 'meta_value' => $value]);
            }
            $this->_meta_caches[$key] = $value;//cache it so we don't have to query again
            return $meta;
        }
    }
    public function addMeta($key,$value){
        $this->_init_meta_caches();
        $meta = $this->meta_relation()->where(['meta_key'=>$key])->first();
        if(!$meta) {
            $meta=$this->meta_relation()->create(['meta_key' => $key, 'meta_value' => $value]);
        }
        return $meta;
    }
    public function deleteMeta($key){
        $keys=is_array($key)?$key:func_get_args();
        $this->_init_meta_caches();
        $this->meta_relation()->whereIn('meta_key',$keys)->delete();
        foreach ($keys as $k) {
            unset($this->_meta_caches[$k]);
        }
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
        if(property_exists($this,'fillable_meta')) {
            if (in_array($name, $this->fillable_meta)){
                return $this->meta($name,$value);
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
    function fillMeta(&$attributes){
        if(property_exists($this,'fillable_meta')) {
            $_meta_attributes = array_flip($this->fillable_meta);
            $meta = array_intersect_key($attributes, $_meta_attributes);
            foreach($meta as $key=>$value) {
                $this->$key=$value;
            }
            unset($attributes['meta']);
        }
    }
    function toArray()
    {
        $array=parent::toArray();
        $array['meta']=$this->meta;
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
    function getHidden(){
        $hidden=parent::getHidden();
        return array_merge($hidden,['meta_relation']);
    }
    protected function getMetaAttribute(){
        $this->_init_meta_caches();
        return $this->_meta_caches;
    }
}