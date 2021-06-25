<?php
namespace App\Model\Traits\User;
use App\Model\Traits\StatusTrait;

trait UserStatus{
    use StatusTrait;
    protected static $_defaultStatus='new';
    static function getPendingStatuses(){
        return ['verified'];
    }
    function isActive(){
        return $this->status=='active';
    }
    function isPending(){
        return in_array($this->status,static::getPendingStatuses());
    }
    function isActivated(){
        return $this->isActive();
    }
    function isNew(){
        return $this->status=='new';
    }
    function isSuspended(){
        return $this->status=='suspended';
    }
}