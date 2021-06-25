<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('/feed', 'FeedController@newPosts')->name('feed');
Route::get('/updated', 'FeedController@updatedPosts')->name('updated');
Route::get('/search', 'FeedController@search')->name('search');
Route::get('/tag/{tag}', 'FeedController@hashTag')->name('tag');
Route::get('/tags', 'TagsController@index')->name('tags');
Route::get('/members', 'MembersController@index')->name('members');
Route::get('/member/{id}/avatar', 'MembersController@avatar')->name('member.avatar');
Route::get('/member/{id}', 'FeedController@member')->name('member');
Route::get('/m/{id}', 'FeedController@member')->name('m');
// Authentication Routes...

$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->get('logout', 'Auth\LoginController@logout')->name('logout');

//Privacy & terms routes

Route::get('/terms', 'PrivacyController@terms')->name('terms');
Route::get('/privacy', 'PrivacyController@privacy')->name('privacy');
Route::get('/disclaimer', 'PrivacyController@disclaimer')->name('disclaimer');


$this->get('/wp-admin',function(){
    return redirect()->to('wp-login.php?redirect_to='.urlencode(url('/')).'&reauth=1');
});

// Registration Routes...
//$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//$this->post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
//$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//$this->post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['prefix'=>'login/{provider}'],
    function(){
        $this->get('/','Auth\AuthController@socialRedirect')->where('provider','facebook|google');
        $this->get('/callback','Auth\AuthController@socialHandle')->where('provider','facebook|google');
    }
);
