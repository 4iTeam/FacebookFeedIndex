<?php
namespace App\Services\Facebook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class FacebookRestLogin{
    protected $apiKey;
    protected $apiSecret;
    protected $data=[];
    protected $endpoint='https://api.facebook.com/restserver.php';
    protected $client;
    protected $response;
    protected $logged;
    protected $debug=false;
    public function __construct()
    {
        $this->apiKey='882a8490361da98702bf97a021ddc14d';
        $this->apiSecret='62f8ce9f74b12f84c123cc23437a4a32';
        $this->client=new Client();
        $this->reset();
    }
    public function reset(){
        $this->logged=$this->response=[];
        $this->setData([
            "api_key" => $this->apiKey,
            "email" => '',
            "password" => '',
            "format" => "JSON",
            "locale" => "vi_vn",
            "method" => "auth.login",
            "return_ssl_resources" => "0",
            "v" => "1.0"
        ]);
    }
    public function login($user,$pass){
        $this->reset();
        $this->setData('email',$user)
            ->setData('password',$pass);
        $this->setData('sig',$this->createSig());
        try {
            $response = $this->getClient()->post($this->endpoint, ['form_params' => $this->data]);
        }catch (TransferException $e){
            if($this->debug){
                throw $e;
            }
            return false;
        }
        @$this->response=json_decode($response->getBody(),true);
        if(empty($this->response['error_code'])){
            $this->logged=$this->response;
            return $this->logged;
        }
        return false;
    }
    public function getAccessToken($user='',$pass=''){
        if($user){
            $this->login($user,$pass);
        }
        return isset($this->logged['access_token'])?$this->logged['access_token']:null;
    }
    protected function createSig(){
        $data='';
        ksort($this->data);
        foreach ($this->data as $key=>$value){
            $data.="$key=$value";
        }
        $data.=$this->apiSecret;
        return md5($data);
    }

    protected function getClient(){
        return $this->client;
    }
    function setData($key,$value=null){
        if(is_array($key)){
            $this->data=$key;
        }else {
            $this->data[$key] = $value;
        }
        return $this;
    }
}