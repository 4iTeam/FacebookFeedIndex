<?php
namespace App\Services\Base;
use App\Model\Token;

abstract class Container{
    /**
     * Config key for access token
     * @var string
     */
    protected $token_field='access_token';
	/**
	 * Service config
	 * @var array
	 *
	 */
	protected $config=[];
	/**
	 * service client
	 * @var mixed
	 */
	protected $client;
	/**
	 * Debug mode
	 * @var bool
	 */
	protected $debug=false;

	function __construct($config=[]) {
		if($config){
			$this->setConfig($config);
		}else {
			$this->defaultConfig();
		}
	}
	/**
	 * @param array $config
	 *
	 * @return static
	 */
	function make($config=[]){
		$instance=new static($this->getConfig());
		$instance->mergeConfig($config);
		return $instance;
	}

    /**
     * Make new instance with config
     * @param array $config
     * @return static
     */
    function withConfig(array $config){
        return $this->make($config);
    }
	abstract protected function defaultConfig();
	abstract protected function createClient($config=[]);
	function getClient($config=[]){
		if(isset($this->client)){
			return $this->client;
		}
		$config=array_merge($this->config,$config);
		$this->client=$this->createClient($config);
		return $this->client;
	}
	function setClient($client){
		$this->client=$client;
		return $this;
	}
	/**
	 * @param $token
	 *
	 * @return static
	 */
	function withToken($token){
	    if(is_numeric($token) && class_exists(Token::class)){
	        $token=Token::find($token);
        }
        if($token instanceof Token){
            $token=$token->token;
        }
		return $this->make([ $this->token_field =>$token]);
	}
	function withAccessToken($token){
		return $this->withToken($token);
	}

	function setConfig($key,$val=''){
		if(is_array($key)){
			$this->config=$key;
		}else{
			$this->config[$key]=$val;
		}
		return $this;
	}
	function mergeConfig(array $config){
	    if($config) {
            $this->config = array_merge($this->config, $config);
        }
	    return $this;
    }


	/**
	 * @return Token|string
	 */
	function getAccessToken(){
		$token=$this->config[$this->token_field];
		return $token;
	}
	function setAccessToken($token){
		if($token instanceof Token){
			$token=$token->token;
		}
		$this->setConfig($this->token_field,$token);
	}
	protected function accessTokenConfigKey(){
	    if(property_exists($this,'token_field')){
	        return $this->token_field;
        }
        return 'access_token';
    }

	function debug(){
		$this->debug=true;
		return $this;
	}
	function getConfig(){
		return $this->config;
	}
}