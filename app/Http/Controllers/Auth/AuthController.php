<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Model\Fixed\Provider;
use App\Model\Token;
use App\Support\Facades\FacebookID;
use App\Support\Facades\Message;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
	public function socialRedirect($provider) {
		//echo $provider;die;
		/**
		 * @var AbstractProvider $driver
		 */
		$driver=Socialite::driver($provider)->with(['provider' => $provider]);
		if($driver instanceof FacebookProvider){
		}

		$driver->redirectUrl(url('login/'.$provider.'/callback'));
		return $driver->redirect();
	}

	public function socialHandle($provider, \Illuminate\Routing\UrlGenerator $generator) {

		try {
			$driver = Socialite::driver($provider);
			$driver->redirectUrl(url('login/'.$provider.'/callback'));
			if($driver instanceof FacebookProvider){
				$fields = ['name', 'email', 'gender', 'verified','first_name','last_name','birthday'];
				$driver->fields($fields);
			}
			$user=$driver->user();
		}catch (InvalidStateException $e){
			Message::error($e->getMessage());
			return redirect(url('login'));
		}catch (ClientException $e){
			Message::error($e->getMessage());
			return redirect(url('login'));
		}
		/**
		 * @var $user AbstractUser
		 */
		if(!$user->getEmail()){
		    if($provider==='facebook') {
                $user->email = $user->getId() . '@facebook.com';
            }
		}
		if(!$user->getName()){
			Message::error('No Name provided');
			return redirect(url('login'));
		}

		if($user instanceof \Laravel\Socialite\Two\User){
			if($user->expiresIn) {
				$expired_at = Carbon::now()->addSeconds( $user->expiresIn );
			}else{
				//$expired_at = Carbon::now()->addYears(10);
				$expired_at = null;
			}
			$email=$user->getEmail();

            $token=[
                'uid'=>$user->getId(),
                'name'=>$user->getName(),
                'email'=>$email,
                'token'=>$user->token,
                'expired_at'=>$expired_at,
                'provider'=>$provider,
                'type'=>Token::LOGIN,
            ];
            $token=Token::add($token);//social info
			if($token){
                /**
                 * @var $u User
                 */
			    if(!$u=$token->user) {
                    if (!$u = User::findByEmail($email)) {
                        $u = $this->createUser($user, $provider);
                    }
                    //user not connected to social profile
                    if(!$u){//user not created
                        Message::error('Cannot create user!');
                        return redirect(url('login'));
                    }
                    $token->user_id=$u->id;
                    $token->save();
                }

                if($u->isActive()) {
                    Auth::login($u);
                    return $this->handleUserLoggedIn($u);
                }
                return $this->handleUserNotActive($u);
            }
		}
		return redirect(url('login'));

		// $user->token;
	}

	function createUser(AbstractUser $user,$provider=''){
		$u=User::create([
			'email'=>$user->getEmail(),
			'name'=>$user->getName(),
			'password'=>Hash::make(str_random(16)),
			'status'=>'active',
		]);
		$u->assignRole('user');
		return $u;
	}
	/**
	 * @param User $user
	 * @return mixed
	 */
	protected function handleUserNotActive($user){
		return redirect()->back()
                 ->withErrors([
	                 'error' => 'User is suspended',
                 ]);
	}
	protected function getRedirectUrlForUser(User $u) {
		if($u->can('access_admin') && $this->routeExists(admin_url())){
			return admin_url();
		}
		return url('/');
	}
	protected function handleUserLoggedIn(User $u){
        //FacebookID::getUserIdWithoutAppScope($u);
		return redirect()->intended();
	}
	protected function routeExists($url){
		$routes = Route::getRoutes();
		$request = Request::create($url);
		try {
			$routes->match($request);
			return true;
		}
		catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e){
			return false;
		}
	}


}
