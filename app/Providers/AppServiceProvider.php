<?php

namespace App\Providers;

use App\Services\Facebook\FacebookService;
use App\Services\Facebook\FacebookIDService;
use App\Services\Facebook\Socialite\FacebookProvider;
use App\Support\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Model\Site;
class AppServiceProvider extends ServiceProvider {

	public function boot(Request $request) {
        $this->cloudFlare();
        $this->extendSocialite();
	}

    public function register() {
        $this->registerStatus();
        $this->registerFacebook();
        Helper::autoload(app_path('Support/helpers'));
    }

	function extendSocialite() {
		Socialite::extend( 'facebook', function (){
            $config=$this->app['config']['services.facebook'];
			$provider= Socialite::buildProvider(
			    FacebookProvider::class, $config
            );
			$site=$this->app['site'];
			/**
             * @var FacebookProvider $provider
             */
			if($site->user) {

            }
            return $provider;
		} );
	}



	function registerFacebook() {
		$this->app->singleton( FacebookService::class );
	}

	function registerStatus() {
		User::registerStatus( [
			'new'      => [
				'label'       => 'New',
				'description' => 'New registered user',
			],
			'active'   => [
				'label'       => 'Active',
				'description' => 'Active user'
			],
			'suspended'  => [
				'label'       => 'Suspended',
				'description' => 'Suspended user',
			]
		] );

	}
	protected function cloudFlare(){
        if($cf=$this->app->request->server('HTTP_CF_VISITOR')){
            if(strpos($cf,'https')){
                $this->app->request->server->set('HTTPS','on');
            }
        }
    }
}
