<?php
namespace App\Model\Meta;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model{
    protected $table='user_meta';
    protected $fillable = ['user_id','meta_key','meta_value'];
    public $timestamps = false;
    function getMetaValueAttribute($value){
        return maybe_unserialize($value);
    }
    function setMetaValueAttribute($value){
        $this->attributes['meta_value'] = maybe_serialize($value);
    }
}