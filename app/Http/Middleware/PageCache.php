<?php


namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PageCache
{
	function handle(Request $request,\Closure $next){
		if(!static::enabled()){
			return $next($request);
		}
		$key=static::cacheKey($request);
		if($cached=Cache::get($key)){
			$response=new \Illuminate\Http\Response($cached['c'],200,$cached['h']);
			$response->header('x-4it-c',1);
			return $response;
		}
		$response=$next($request);
		/**
		 * @var \Symfony\Component\HttpFoundation\Response $response
		 */

		if ($this->shouldCache($request, $response)) {
			Cache::put($key,[
				'c'=>$response->getContent(),
				'h'=>$response->headers->all()
			],1);
		}
		return $response;
	}
	public static function enabled(){
		return config('cache.web');
	}
	public static function cacheKey(Request $request){
		$path=$request->path();
		$query=$request->query();
		$path.=json_encode(Arr::only($query,['page','q']));
		return 'page_cache:'.$path;
	}
	/**
	 * Determines whether the given request/response pair should be cached.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  \Symfony\Component\HttpFoundation\Response  $response
	 * @return bool
	 */
	protected function shouldCache(Request $request, Response $response)
	{
		return $request->isMethod('GET') && $response->getStatusCode() == 200;
	}
}