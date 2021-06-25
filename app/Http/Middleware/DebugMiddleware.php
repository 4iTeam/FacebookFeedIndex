<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\DB;
class DebugMiddleware{
    protected $debug;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if($this->isDebug()){
            DB::enableQueryLog();
        }

        return $next($request);
    }
    function isDebug(){
        $debug=get_option('debug');
        if($debug && request()->input('debug')===$debug) {
            return true;
        }
        return false;
    }
    function terminate(){
        if($this->isDebug()) {
            dd(DB::getQueryLog());
        }
    }

}