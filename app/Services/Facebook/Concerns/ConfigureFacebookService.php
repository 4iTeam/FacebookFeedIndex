<?php
namespace App\Services\Facebook\Concerns;
use App\Services\Facebook\PersistentData\FacebookMemoryPersistentDataHandler;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use App\Model\Token;
use App\Services\Facebook\FacebookService;
use Facebook\GraphNodes\GraphEdge;
use Closure;

trait ConfigureFacebookService{

    protected function findAccessToken(){
        $type='login';
        if(is_installing()){
            return '';
        }
        if($user=current_user()){
            if($token=$user->facebookTokens()->first()){
                return $token;
            }
        }
        if(\Schema::hasTable('tokens')) {
            if ($token = Token::where(['type' => $type, 'provider' => Token::FACEBOOK])->latest(Token::EXPIRED_AT)->first())
                return $token->token;
        }
        return '';
    }
    protected function defaultConfig(){
        if(!$this->config){
            $facebook=config('services.facebook');
            $facebook['app_id']=$facebook['client_id'];
            $facebook['app_secret']=$facebook['client_secret'];
            unset($facebook['client_id'],$facebook['client_secret']);
            $this->config=$facebook;
        }

        if(!is_array($this->config)){
            $this->config=[];
        }
        $default=[
            'persistent_data_handler'=> new FacebookMemoryPersistentDataHandler(),
            'default_graph_version' => 'v6.0',
        ];
        $this->config=array_merge($default,$this->config);
        $this->setAccessToken($this->findAccessToken());
    }

    /**
     * @param $app_id
     * @param string $app_secret
     *
     * @return FacebookService
     */
    function withApp($app_id,$app_secret=''){
        if(!is_array($app_id)){
            $app_id=[
                'app_id'=>$app_id,
            ];
            if($app_secret){
                $app_id['app_secret']=$app_secret;
            }
        }
        return $this->make($app_id);
    }


    protected function createClient($config=[]) {
        if(isset($config['access_token']) && !isset($config['default_access_token'])){
            $config['default_access_token']=$config['access_token'];
        }
        return $this->clientInit(new Facebook($config));
    }
    protected function clientInit($client){
        return $client;
    }

    /**
     * @param array $config
     *
     * @return \Facebook\Facebook
     */
    function getFb($config=[]){
        return $this->getClient($config);
    }

    function setFb(Facebook $facebook){
        return $this->setClient($facebook);
    }

    function getAll($endpoint, $params=[] ,$accessToken = null, $eTag = null, $graphVersion = null){
        $nextFeed=$results=$this->getEdge($endpoint,$params,$accessToken,$eTag,$graphVersion);
        if($results){
            while($nextFeed = $this->next($nextFeed)){
                foreach ($nextFeed as $item) {
                    $results[]=$item;
                }
            }
        }
        return $results;
    }
    function getAsync($endPoint,$params=[],Closure $callback,$accessToken = null, $eTag = null, $graphVersion = null){
        $results=
            retrieve(function()use ($endPoint,$params,$accessToken,$eTag,$graphVersion){
                return $this->getEdge($endPoint,$params,$accessToken,$eTag,$graphVersion);
            });
        if($results instanceof GraphEdge){
            $isStop=$callback($results)===false;
            while(!$isStop && ($results = retrieve(function()use($results){
                return $this->next($results);
            }))) {
                $callback($results);
            }
        }
    }

    /**
     * @param $edge
     * @return \Facebook\GraphNodes\GraphEdge|null
     */
    function next(GraphEdge $edge){
        return $this->getFb()->next($edge);
    }
    /**
     * @param $endpoint
     * @param $params
     * @param null $accessToken
     * @param null $eTag
     * @param null $graphVersion
     *
     * @return array
     * @throws FacebookSDKException
     */
    function get($endpoint, $params=[] ,$accessToken = null, $eTag = null, $graphVersion = null){
        return $this->sendRequest(
            'GET',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }
    public function post($endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null){
        return $this->sendRequest(
            'POST',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }
    public function delete($endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null){
        return $this->sendRequest(
            'DELETE',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }
    protected function _sendRequest($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null){
        $fb=$this->getFb();
        return $fb->sendRequest($method, $endpoint, $params, $accessToken, $eTag, $graphVersion );
    }
    function sendRequest($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null){

        try {
            $response=$this->_sendRequest($method,$endpoint,$params,$accessToken,$eTag,$graphVersion);
            return $response->getDecodedBody();
        }catch (FacebookSDKException $e){
            if(!$this->debug) {
                return [];
            }else{
                throw $e;
            }
        }
    }
    function getEdge($endpoint, $params=[] ,$accessToken = null, $eTag = null, $graphVersion = null){
        return $this->sendRequestEdge(
            'GET',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $params
     * @param null $accessToken
     * @param null $eTag
     * @param null $graphVersion
     * @return array|\Facebook\GraphNodes\GraphEdge
     * @throws FacebookSDKException
     */
    protected function sendRequestEdge($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null){
        try {
            $response = $this->_sendRequest($method, $endpoint, $params, $accessToken, $eTag, $graphVersion );
            return $response->getGraphEdge();
        }catch (FacebookSDKException $e){
            if(!$this->debug) {
                return [];
            }else{
                throw $e;
            }
        }
    }

}
