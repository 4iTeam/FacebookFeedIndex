<?php
namespace App\Model\Fixed;
class Provider{
	const GOOGLE='gg';
	const MICROSOFT='ms';
	const FACEBOOK='fb';
	static $id_maps=[
		'google'=>self::GOOGLE,
		'facebook'=>self::FACEBOOK,
		'microsoft'=>self::MICROSOFT,
	];
	static $name_maps;

	/**
	 * Full to short
	 */
	public static function id($name){
		$name=trim(strtolower($name));
		return isset(static::$id_maps[$name])?static::$id_maps[$name]:$name;
	}
	public static function name($id){
		$id=trim(strtolower($id));
		return isset(static::$name_maps[$id])?static::$name_maps[$id]:$id;
	}
	public static function render($id,$mode='both'){
		switch ($mode){
			case 'both':
				return static::name($id).' '.static::getIcon($id);
			case 'name':
				return static::name($id);
			case 'icon':
				return static::getIcon($id);
		}

	}
	public static function getIcon($id){
		return sprintf('<i class="fa fc %s" title="%s"></i>',static::getIconClass($id),static::name($id));
	}
	static function getIconClass($id){
		switch($id){
			case self::GOOGLE:
				return 'fa-google';
			case self::MICROSOFT:
				return 'fa-windows';
			case self::FACEBOOK:
				return 'fa-facebook-official';
		}
		return '';
	}
}
Provider::$name_maps=array_flip(Provider::$id_maps);
