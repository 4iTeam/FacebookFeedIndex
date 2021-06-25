<?php
namespace App\Model;
use App\Model;
use App\Sites\Modules\Managers\SiteModuleManager;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

/**
 * Class Site
 * @package App\Model
 * @property $id
 * @property $domain
 * @property $group_id
 * @property $user_id
 * @property $owner_id
 * @property $name
 * @property $short_name
 * @property $app_id
 * @property $app_secret
 * @property $parent_id
 * Meta Fields:
 * @property $logo
 * @property $title
 * @property $description
 * @property $image
 * @property $favicon
 * @property $copy
 * @property $hide_credit
 * @property $ga_track_id
 * @property $googl_key
 * @property $alias_mode
 * @property $lockers
 * Relations:
 * @property User $owner
 * @property Site $parent

 */
class Site extends Model {
    protected $fillable=['domain','name','owner_id','parent_id'];
    protected $fillable_meta=['title','description','logo','image','favicon','copy',
        'ga_track_id',
    ];
    protected $hidden=['meta'];
    /**
     * @var Site The Alias site
     */
    protected $theAlias;
    use Model\Traits\MetaField;

    /**
     * @param $domain
     * @return static
     */
    static function findByHost($domain){
        $host=str_replace('www.','',$domain);
        $host=strtolower($host);
        return static::where('domain',$host)->first();
    }
    static function findByDomain($domain){
        return static::where('domain',$domain)->first();
    }
    function owner(){
        return $this->belongsTo(User::class);
    }
    function setupFacebookConfig($facebook){
        if(!is_array($facebook)){
            $facebook=[];
        }
        $facebook=array_merge($facebook,$this->getFacebookConfig());
        $this->setFacebookConfig($facebook);
        return $facebook;
    }
    function getFacebookConfig(){
        $config=[];
        if($this->app_id){
            $config['client_id']=$this->app_id;
        }
        if($this->app_secret){
            $config['client_secret']=$this->app_secret;
        }
        return $config;
    }
    function setFacebookConfig($config){
        if(isset($config['client_id'])){
            $this->app_id=$config['client_id'];
        }
        if(isset($config['client_secret'])){
            $this->app_secret=$config['client_secret'];
        }
    }
    function isRoot(){
        $rootDomains=[];
        return in_array($this->domain,$rootDomains);
    }
    function hideCredit(){
        return $this->isRoot()||$this->hide_credit;
    }
    function maybeRedirect(){
        if(!$this->exists){
            return false;
        }
        if(!$this->isAlias() || ($this->isAlias() && $this->alias_mode=='redirect')){
            if($redirect=$this->chroot()){
                return $redirect->current();
            }
        }
        return false;
    }
    function maybeChroot(){
        if($this->isAlias() && $this->alias_mode=='chroot'){
            $this->chroot();
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Routing\UrlGenerator|false
     */
    function chroot(Request $request=null){
        if($request===null){
            $request=request();
        }
        $host = $request->getHost();
        if($host===$this->domain){//we are on same host as site
            return false;
        }
        $root = $request->root();
        $urlGenerator=url();
        /**
         * @var UrlGenerator $urlGenerator
         */

        $root = str_replace($host, $this->domain, $root);

        $urlGenerator->forceRootUrl($root);
        return $urlGenerator;
    }

    /**
     * Merge from default to this instance
     * @param Site $site
     */
    function mergeDefault(Site $site){
        foreach ($site->toArray() as $k=>$v){
            if(!$this->$k){
                $this->$k=$v;
            }
        }
    }



    function hasParentSite(){
        return $this->parent_id && $this->parent;
    }
    function setAlias(Site $alias){
        $this->theAlias=$alias;
        return $this;
    }
    function isAlias(){
        return $this->theAlias;
    }
    function parent(){
        return $this->belongsTo(Site::class);
    }
    function modules(){
        return $this->belongsToMany(Module::class)->withTimestamps()->withPivot('settings');
    }

    /**
     * @return SiteModuleManager
     */
    function getModuleManager(){
        return app(SiteModuleManager::class);
    }
    function module($name){
        return $this->getModuleManager()->driver($name);
    }
    function hasModule($name){
        return $this->getModuleManager()->hasDriver($name);
    }
    protected function setUserIdAttribute($value){
        $this->attributes['user_id']=absint($value);
    }
    protected function getGaTrackIdAttributeMeta($value){
        if($this->exists){
            return $value;
        }
        return get_option('ga_track_id');
    }
    protected function getTitleAttributeMeta($value){
        if(false===strpos($value,$this->name)) {
            return $value . ' | ' . $this->name;
        }
        return $value;
    }


}