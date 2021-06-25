<?php
namespace App\Http\Controllers;
use App\Admin\Exceptions\PermissionDenied;
use App\Support\Facades\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Menu;
abstract class AdminBaseController extends BaseController {

	use DispatchesJobs, ValidatesRequests,AuthorizesRequests;
	function withInput($inputs=[],$merge=[]){
		if(empty($inputs)){
			$inputs=request()->input();
		}
		if(method_exists($inputs,'toArray')){
			$inputs=$inputs->toArray();
		}
		if(is_array($inputs)) {
		    $inputs=array_merge($inputs,$merge);
			if(request()->session()->has('errors')) {
				$inputs=array_merge($inputs,request()->old());
			}
			request()->session()->flashInput($inputs);
		}
		return $this;
	}
	function withError($message){
		Message::error($message);
		return $this;
	}
	function withSuccess($message){
		Message::success($message);
		return $this;
	}
	function withWarning($message){
		Message::warning($message);
		return $this;
	}
	function withInfo($message){
		Message::info($message);
		return $this;
	}
	function withRawMessage(){
		Message::setRaw();
		return $this;
	}
	function withTemplate($template){
		Message::withTemplate($template);
		return $this;
	}

	function back(){
		return redirect()->back();
	}
	function redirect($path, $status = 302, $headers = [], $secure = null){
		throw new HttpResponseException(redirect()->to($path, $status , $headers , $secure ));
	}

	function activeMenu($name){
		$menu=Menu::get('AdminMenu')->get($name);
		if($menu){
			$menu->activate();
		}
		return $this;
	}
	function requirePermission($cap){
		if(!call_user_func_array('current_user_can',func_get_args())){
			$this->accessDenied();
		}
	}
	function accessDenied(){
		throw new PermissionDenied();
	}
	function ajaxSuccess($message='',$args=[]){
        if(is_array($message)){
            $o_args=$args;
            $args=['data'=>$message];
            $message='';
            if(is_string($o_args)){
                $message=$o_args;
            }
        }
		$result=['success'=>1,'message'=>$message];
		return array_merge($result,$args);
	}
	function ajaxError($message='',$args=[]){
        if(is_array($message)){
            $o_args=$args;
            $args=['data'=>$message];
            $message='';
            if(is_string($o_args)){
                $message=$o_args;
            }
        }
		$result=['success'=>0,'message'=>$message];
		$result= array_merge($result,$args);
		return response()->json($result);
	}
	protected function _call(){

	}

	/**
	 * @return \App\User|null
	 */
	function user(){
		return current_user();
	}
	function index_url($parameters=[]){
		if(property_exists($this,'index_url')) {
			return admin_url( $this->index_url ,$parameters);
		}else{
			return admin_url('',$parameters);
		}
	}

}
