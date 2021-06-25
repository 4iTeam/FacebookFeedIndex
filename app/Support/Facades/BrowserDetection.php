<?php
namespace App\Support\Facades;
use App\Services\Utils\SimpleBrowserDetect;
use Illuminate\Support\Facades\Facade;

class BrowserDetection extends Facade {
	protected static function getFacadeAccessor()
	{
		return SimpleBrowserDetect::class;
	}

	/**
	 * @return SimpleBrowserDetect
	 */
	public static function make(){
		return static::getFacadeRoot();
	}
}