<?php
namespace App\Http\Middleware;
use App\Exceptions\UserSuspendedException;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
class Authenticate extends BaseAuthenticate{
	public function handle( $request, Closure $next, ...$guards ) {
		$response = parent::handle( $request, $next, $guards );
		if(current_user()->isSuspended()){
			throw new UserSuspendedException();
		}
		return $response;
	}
}