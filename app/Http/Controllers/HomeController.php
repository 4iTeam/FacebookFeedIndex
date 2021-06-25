<?php

namespace App\Http\Controllers;


use App\Model\Facebook\Post;
use App\Model\Facebook\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=[
            'members'=>User::query()->count(),
            'posts'=>Post::query()->count(),
            'comments'=>Post::query()->sum('comment_count'),
            'reactions'=>Post::query()->sum('reaction_count'),
            'shares'=>Post::query()->sum('share_count'),
            'feed'=>Post::query()->select()->addSelect(DB::raw('(comment_count*2) + (share_count*4) + reaction_count as popularity_score'))->orderBy('popularity_score','desc')->paginate(),
        ];
        return view('pages.home',$data);
    }
}
