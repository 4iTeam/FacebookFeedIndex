<?php
namespace App\Services\Facebook;
use App\Services\Base\Container;
use App\Services\Facebook\Concerns\ConfigureFacebookService;
use App\Services\Facebook\Concerns\InteractsWithFacebook;


class FacebookService extends Container {
    protected $token_field='default_access_token';
    use ConfigureFacebookService,
        InteractsWithFacebook;
}