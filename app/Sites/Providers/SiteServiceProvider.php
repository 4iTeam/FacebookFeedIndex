<?php

namespace App\Sites\Providers;

use App\Http\Middleware\PageCache;
use App\Sites\Middleware\SiteMiddleware;
use App\Sites\Modules\Managers\SiteModuleManager;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use App\Model\Site;
class SiteServiceProvider extends ServiceProvider {
    public function boot(Kernel $kernel, Router $router, Request $request) {
        $this->loadViewsFrom(app_path('Sites/views'),'s');
		$this->servePageCache($request);
        if($kernel instanceof \Illuminate\Foundation\Http\Kernel){
            $kernel->pushMiddleware(SiteMiddleware::class);
        }
    }
    public function servePageCache($request){
    	if($this->app->runningInConsole()){
    		return ;
		}
		if(PageCache::enabled()) {
			$key = PageCache::cacheKey($request);
			if ($cached = Cache::get($key)) {
				$response = new \Illuminate\Http\Response($cached['c'], 200, $cached['h']);
				$response->header('x-4it-c', 1);
				throw new HttpResponseException($response);
			}
		}
	}
    public function register(){
        $this->multiSiteSetup();
        $this->app->singleton(SiteModuleManager::class);
    }
    function multiSiteSetup(){
        $this->app->singleton('site',function(){
            $siteDefault=$this->defaultSite();
            if($this->app->runningInConsole()){
                //don't need to setup site when in console, since we don't have host
                view()->share('site',$siteDefault);
                return $siteDefault;
            }
            /**
             * @var Request $request
             */
            $request=$this->app['request'];
            if(1 || !$site = Site::findByHost($request->getHost())){
                $site=$siteDefault;
            }else{//Site exists
                if($site->hasParentSite()){
                    $tmp=$site;
                    $site=$site->parent;
                    $site->setAlias($tmp);
                }
                $site->mergeDefault($siteDefault);

            }
            /**
             * @var \Illuminate\Config\Repository $config
             */
            $config=$this->app['config'];
            $facebook=$config['services.facebook'];
            $facebook=$site->setupFacebookConfig($facebook);
            $config->set('services.facebook',$facebook);
            view()->share('site',$site);
            return $site;
        });
        $this->app->alias('site',Site::class);
    }
    function defaultSite(){
        $site=new Site();
        $site->id=0;
        $site->domain='laraveladmin.local';
        $site->name='4IT Community';
        $site->short_name='LA';
        $site->title='Nơi lưu trữ của 4IT Community';
        $site->description='Xem và tìm kiếm bài viết trong nhóm 4IT Community một cách nhanh nhất';
        $site->image=url('images/logo-256.png');
        return $site;
    }

}
