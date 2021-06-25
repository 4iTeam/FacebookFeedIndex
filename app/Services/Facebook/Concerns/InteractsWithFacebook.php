<?php
namespace App\Services\Facebook\Concerns;
use Facebook\Facebook;

trait InteractsWithFacebook{
    function getPostLikeComment($id,$comment=1000,$reaction=1000){
        if(!$id){
            return [];
        }
        $id=sprintf('%s?fields=comments.order(reverse_chronological).limit(%d){from},reactions.limit(%d){id}',$id,$comment,$reaction);
        return $this->get($id);
    }
    function getPostLike($id,$limit=1000){
        if(!$id){
            return [];
        }
        $id=sprintf('%s?fields=reactions.limit(%d){id}',$id,$limit);
        return $this->get($id);
    }
    function getPostComment($id,$limit=1000,$field='from'){
        if(!$id){
            return [];
        }
        $id=sprintf('%s?fields=comments.order(reverse_chronological).limit(%d){%s}',$id,$limit,$field);
        return $this->get($id);
    }
    function getGroup($id){
        $group=$this->get($id,['fields'=>'id,name,email']);
        if($group && $group['email']){
            $group['slug']=substr($group['email'],0,strpos($group['email'],'@'));
        }
        return $group;
    }
    function getGroupMembers($id,$limit=100,$page=''){
        $params=['limit'=>$limit];
        if($page){
            $params['after']=$page;
        }
        $params['fields']='id,name';
        return $this->get($id . '/members',$params);
    }
    function getComments($id,$limit=100,$page='',$params=[]){
        $params['limit']=$limit;
        if(!isset($params['order']))
            $params['order']='reverse_chronological';
        if($page){
            $params['after']=$page;
        }
        return $this->get($id . '/comments',$params);
    }
    function getReactions($id,$limit=100,$page='',$params=[]){
        $params['limit']=$limit;
        if($page){
            $params['after']=$page;
        }
        return $this->get($id . '/reactions',$params);
    }
    function getTokenMeta($accessToken){
        $fb=$this->getFb();
        /**
         * @var Facebook $fb
         */
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        return $tokenMetadata;
    }
    function debugToken($token){
        return $this->get('/debug_token',['input_token'=>$token]);
    }
    function me(){
        if($appid=$this->isAppToken()){
            return $this->get("/$appid");
        }
        return $this->get('/me');
    }
    function isAppToken($token=''){
        if(!$token){
            $token=$this->getAccessToken();
        }
        $tokens=explode('|',$token);
        if(count($tokens)==2){
            return $tokens[0];
        }
        return false;
    }
}