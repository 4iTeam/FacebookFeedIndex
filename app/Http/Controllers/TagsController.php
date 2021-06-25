<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 04-Apr-18
 * Time: 9:56 PM
 */

namespace App\Http\Controllers;


use App\Model\Facebook\Tag;

class TagsController extends Controller
{
    function index(){
        site()->title='Danh sách #hashtag';
        $query=Tag::query();
        if($q=request('q')){
            $query->where('tag','like','%'.$q.'%');
        }
        $query->latest('post_count');
        $tags=$query->paginate(500);
        return view('pages.tags',['tags'=>$tags]);
    }
}