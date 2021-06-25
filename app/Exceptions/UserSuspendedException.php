<?php
namespace App\Exceptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
class UserSuspendedException extends HttpResponseException {
	function __construct() {
		parent::__construct(new Response());
	}

	function getResponse() {
		if(request()->ajax()) {
			return response()->json(['status'=>'error','success'=>0,'message'=>'You do not have sufficient permissions to access this page'],403);
		}else{
			return response(view('admin::errors.permission'), 403);
		}
	}
}