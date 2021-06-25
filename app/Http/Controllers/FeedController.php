<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 22-Mar-18
 * Time: 5:15 PM
 */

namespace App\Http\Controllers;


use App\Model\Facebook\Post;
use App\Model\Facebook\Tag;
use App\Model\Facebook\User;
use App\Services\Utils\Hashtag;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedController extends Controller
{
    function __construct()
    {
        $this->middleware(function($request,$next){
            view()->share('tags',Tag::query()->latest('post_count')->limit(10)->get());
            return $next($request);
        });
    }

    function newPosts(){
        $query=$this->searchQuery();
        $query->latest();
        $posts=$query->paginate();
        site()->title='Bài viết mới';
        return view('pages.feed',['posts'=>$posts]);
    }
    function updatedPosts(){
        $query=$this->searchQuery();
        $query->latest('updated_at');
        $posts=$query->paginate();
        site()->title='Bài viết mới cập nhật';
        return view('pages.feed',['posts'=>$posts]);
    }
    function search(){
        site()->title='Tìm kiếm bài viết';
        if(!$q=request('q')){
            $posts=new LengthAwarePaginator([],1,1);
        }else {
            $posts = $this->searchQuery()->latest('updated_at')->paginate();
        }
        return view('pages.feed',['posts'=>$posts->appends(request()->except('page'))]);
    }

    function hashTag($tag){
        site()->title='Bài viết theo hashtag '.$tag;
        $tag=Tag::where('tag',$tag)->firstOrFail();
        if($tag) {
            $query = $tag->posts()->latest('updated_at');
            $this->searchQuery($query);
            $posts=$query->paginate();
        }else{
            $posts = new LengthAwarePaginator([],1,1);
        }
        return view('pages.feed',[
            'posts'=>$posts->appends(request()->except('page')),
            'filtered'=>'Đang tìm kiếm bài viết trong hashtag <span class="badge badge-danger">'.$tag->tag.'</span>'
        ]);
    }
    function member($user){
        $user=User::findBySlug($user);
		$filterText='';
        if($user) {
            site()->title = 'Bài viết của ' . e($user->name);
            $query = $user->posts()->latest('updated_at');
            $this->searchQuery($query);
            $posts=$query->paginate();
            $filterText='Đang tìm kiếm bài viết của <span class="badge badge-info">'.e($user->name).'</span>';
        }else{
            site()->title = 'Không tìm thấy thành viên này!';
            $posts = new LengthAwarePaginator([],1,1);
        }
        return view('pages.feed',[
            'posts'=>$posts->appends(request()->except('page')),
            'filtered'=> $filterText,
        ]);
    }

    /**
     * @param null $query
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    protected function searchQuery($query=null){
        if(!$query){
            $query=Post::query();
        }
        if($q=request('q')){
            $query->where('message','like','%'.$q.'%');
        }
        return $query;
    }
}
