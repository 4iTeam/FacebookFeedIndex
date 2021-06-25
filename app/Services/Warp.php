<?php


namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Warp
{
	protected $apiBase='https://api.cloudflareclient.com';
	protected $apiVersion='v0a778';
	protected $apiUrl;
	protected $regUrl;
	protected $statusUrl;
	protected $client;
	protected $account_id='166d8a69-521d-44db-bf42-cef1812651a3';
	protected $account_token='95d75f58-69e9-4c43-9088-4a24335f7de1';
	public function __construct()
	{
		$this->apiUrl=$this->apiBase.'/'.$this->apiVersion;
		$this->regUrl=$this->apiUrl.'/reg';
		$this->statusUrl=$this->apiUrl.'/client_config';
		$this->client=new Client([
			'headers' => [
				'User-Agent' => 'okhttp/3.12.1',
				"Accept-Encoding"=> "gzip",
			]
		]);
	}
	public function getAccountInfo(){
		$result=$this->client->get(
			$this->getConfigUrl($this->account_id),
			[
				'headers' =>
		        [
					'Authorization' => "Bearer {$this->account_token}",
					'Content-Type' => 'application/json; charset=UTF-8',
				]
		    ]
		);
		echo $result->getBody();
	}
	public function updateAccount($data){
		$result=$this->client->patch(
			$this->getConfigUrl($this->account_id),
			[
				RequestOptions::JSON=>$data,
				'headers' =>
				[
					'Authorization' => "Bearer {$this->account_token}",
					'Content-Type' => 'application/json; charset=UTF-8',
				]
			]
		);
		echo $result->getBody();
	}

	public function getClientConfig(){
		$jwt='95d75f58-69e9-4c43-9088-4a24335f7de1';
		$result=$this->client->get(
			$this->statusUrl,
			['headers' =>
				[
					'Authorization' => "Bearer {$jwt}"
				]
			]
		);
		echo $result->getBody();
	}
	public function regNewAccount($publicKey='',$ref=''){
		if(!$ref){
			$ref=$this->account_id;
		}
		$data = ["install_id"=> "", "tos"=> date('c'), "key"=> $publicKey, "fcm_token"=> "", "type"=> "Android",
			"locale"=> "vi_VN",'referrer'=>$ref];

		$result=$this->client->post(
			$this->regUrl,[
				RequestOptions::JSON=>$data,
				'headers' =>
					[
						'Content-Type' => 'application/json; charset=UTF-8',
					]
			]
		);
		echo $result->getBody();
	}
	public function getConfigUrl($account_id){
		return $this->regUrl.'/'.$account_id.'';
	}
}