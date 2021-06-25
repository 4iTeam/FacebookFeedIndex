<?php

use App\Model\Facebook\User;

Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});
Breadcrumbs::register('terms', function ($breadcrumbs) {
	$breadcrumbs->push('Home', route('home'));
	$breadcrumbs->push('Quy định sử dụng', route('terms'));
});
Breadcrumbs::register('privacy', function ($breadcrumbs) {
	$breadcrumbs->push('Home', route('home'));
	$breadcrumbs->push('Quyền riêng tư', route('privacy'));
});
Breadcrumbs::register('disclaimer', function ($breadcrumbs) {
	$breadcrumbs->push('Home', route('home'));
	$breadcrumbs->push('Tuyên bố từ chối trách nhiệm', route('disclaimer'));
});
Breadcrumbs::register('feed', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Bài mới đăng', route('feed'));
});
Breadcrumbs::register('updated', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Bài mới cập nhật', route('updated'));
});

Breadcrumbs::register('search', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tìm kiếm', route('search'));
});

Breadcrumbs::register('tags', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tags', route('tags'));
});
Breadcrumbs::register('tag', function ($breadcrumbs,$tag) {
    $breadcrumbs->parent('tags');
    $breadcrumbs->push('#'.$tag, route('tag',$tag));
});

Breadcrumbs::register('members', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Thành viên',route('members'));
});

Breadcrumbs::register('member', function ($breadcrumbs,$id) {
    $breadcrumbs->parent('members');
    $user= User::findBySlug($id);
    $name=$user->name??'';
    $breadcrumbs->push('Bài viết của '.$name,route('member',[$id]));
});
Breadcrumbs::register('m', function ($breadcrumbs,$id) {
    $breadcrumbs->parent('members');
    $user= User::findBySlug($id);
    $breadcrumbs->push('Bài viết của '.$user->name??'',route('member',[$id]));
});
