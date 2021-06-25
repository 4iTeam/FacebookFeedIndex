<?php
namespace App\Http\Middleware;
use App\Exceptions\UserSuspendedException;
use Closure;

class CheckUserSuspended{
    public function handle( $request, Closure $next ) {
        if(current_user()&&current_user()->isSuspended()){
            throw new UserSuspendedException();
        }
        return $next($request);
    }
}