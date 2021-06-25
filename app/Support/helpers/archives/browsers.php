<?php
use App\Support\Facades\BrowserDetection;
function is_IE(){
	return BrowserDetection::make()->isIE;
}
function is_winIE(){
	return BrowserDetection::make()->isWinIE;
}
function is_macIE(){
	return BrowserDetection::make()->isMacIE;
}
function is_opera(){
	return BrowserDetection::make()->isOpera;
}
function is_NS4(){
	return BrowserDetection::make()->isNS4;
}
function is_safari(){
	return BrowserDetection::make()->isSafari;
}
function is_chrome(){
	return BrowserDetection::make()->isChrome;
}
function is_iphone(){
	return BrowserDetection::make()->isIphone;
}
function is_edge(){
	return BrowserDetection::make()->isEdge;
}
function is_lynx(){
	return BrowserDetection::make()->isLynx;
}
function is_gecko(){
	return BrowserDetection::make()->isGecko;
}
function qc_is_mobile(){
	if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
		$is_mobile = false;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
	           || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
		$is_mobile = true;
	} else {
		$is_mobile = false;
	}

	return $is_mobile;
}