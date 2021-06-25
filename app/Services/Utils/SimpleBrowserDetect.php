<?php
namespace App\Services\Utils;

class SimpleBrowserDetect{
	var $isLynx=false;
	var $isGecko=false;
	var $isWinIE=false;
	var $isMacIE=false;
	var $isOpera=false;
	var $isNS4=false;
	var $isSafari=false;
	var $isChrome=false;
	var $isIphone=false;
	var $isEdge=false;
	var $isIE=false;
	protected $init=false;
	function init(){
		if($this->init){
			return ;
		}
		$this->init=true;

		if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
			if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false ) {
				$this->isLynx = true;
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Edge' ) !== false ) {
				$this->isEdge = true;
			} elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false ) {
				if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'chromeframe' ) !== false ) {

					$this->isChrome=true;
					$this->isWinIE = ! $this->isChrome;
				} else {
					$this->isChrome = true;
				}
			} elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false ) {
				$this->isSafari = true;
			} elseif ( ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false ) && strpos($_SERVER['HTTP_USER_AGENT'], 'Win') !== false ) {
				$this->isWinIE = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false ) {
				$this->isMacIE = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false ) {
				$this->isGecko = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false ) {
				$this->isOpera = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Nav') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.') !== false ) {
				$this->isNS4 = true;
			}
		}

		if ( $this->isSafari && stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') !== false )
			$this->isIphone = true;

		$this->isIE = ( $this->isMacIE || $this->isWinIE );
	}
	function __construct() {
		$this->init();
	}
	function __call($method){
		return isset($this->$method)&&$this->$method;
	}
}