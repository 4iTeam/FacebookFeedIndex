<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 26-Mar-18
 * Time: 4:27 PM
 */

namespace App\Http\Controllers;


use App\Model\Facebook\User;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MembersController extends Controller
{
	protected $disk;
	function __construct()
	{
		$this->disk=Storage::disk('avatar');
	}

	function index(){
        site()->title='Danh sách thành viên';
        $query=User::query()->latest('post_count')->latest('comment_count');
        if($q=request('q')){
            $query->where('name','like','%'.$q.'%');
        }
            $users=$query->paginate(96);
        return view('pages.users',['users'=>$users]);
    }
    function avatar($slug){
		$cacheKey='member.avatar:'.$slug;
		if(Cache::has($cacheKey)){
			$url=Cache::get($cacheKey);
			return redirect()->to($url);
		}
    	$member=User::findBySlug($slug);
    	if(!$member){
    		abort(404);
		}
		$client = new Client([
			'allow_redirects'=>true,
		]);


		$url = $member->getGraphAvatar();
		$redirectUrl='';
		$res = $client->get(
			$member->getGraphAvatar(),
			[
				'on_stats' => function (TransferStats $stats) use (&$redirectUrl) {
					$redirectUrl = $stats->getEffectiveUri();
				},
			]
		);
		if($redirectUrl){
			Cache::put($cacheKey,$redirectUrl,60*6);
			$url=$redirectUrl;
		}

    	return redirect()->to($url);
	}
}
