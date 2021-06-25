<?php


namespace App\Http\Controllers;


class PrivacyController
{
	public function privacy(){
		return view('pages.privacy');
	}
	public function terms(){
		return view('pages.terms');
	}
	public function disclaimer(){
		return view('pages.disclaimer');
	}

}
