<?php
namespace App\Services\Token;
use App\Model\Token;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class RefreshTokenService{
	protected $config=[];
	function __construct() {
		$this->config['google']=storage_path('/gsuite/client_secret.json');
	}
	function setConfig($provider,$config){
		$this->config[$provider]=$config;
		return $this;
	}
	function refresh(Token $token){
		return false;
	}

}