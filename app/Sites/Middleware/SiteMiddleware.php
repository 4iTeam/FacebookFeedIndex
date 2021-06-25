<?php
namespace App\Sites\Middleware;
use App\Model\Site;
use Illuminate\Http\Request;

class SiteMiddleware{
    protected $site;
    function __construct(Site $site)
    {
        $this->site=$site;
    }
    function handle(Request $request, \Closure $next){
        if($url=$this->site->maybeRedirect()) {//Maybe redirect site based on site setting
            return redirect($url);
        }
        //$this->site->maybeChroot();
        return $next($request);
    }
}