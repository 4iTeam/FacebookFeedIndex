<?php
namespace App\Model\Traits\User;
use App\Model\Meta\UserMeta as MetaClass;
use App\Model\Traits\MetaTrait;
trait UserMetaTrait{
    use MetaTrait;
    function meta_relation(){
        return $this->hasMany(MetaClass::class);
    }
}