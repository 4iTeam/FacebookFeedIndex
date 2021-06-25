<?php
namespace App\Support\Facades;
use App\Services\Facebook\FacebookIDService;
use Illuminate\Support\Facades\Facade;

/**
 * Class FacebookID
 * @package App\Support\Facades
 * @mixin FacebookIDService
 */
class FacebookID extends Facade {
    protected static function getFacadeAccessor()
    {
        return FacebookIDService::class;
    }

    /**
     * @return FacebookIDService
     */
    public static function make(){
        return static::getFacadeRoot();
    }
}