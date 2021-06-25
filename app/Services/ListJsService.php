<?php
namespace App\Services;
use App\Support\ListJs;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class ListJsService{
	protected $request;
	/**
	 * @var ListJs
	 */
	protected $list;
	protected $count;
	protected $next;
	function __construct(Request $request) {
		$this->request=$request;
	}
	abstract function process();
	abstract function query();
	function collection(Collection $collection,$columns='*'){
		$this->list=new ListJs($collection,$columns);
		return $this;
	}
	function addColumn($columns){
		$this->list->addColumn(func_get_args());
		return $this;
	}

	/**
	 * @param $column
	 * @param $callback
	 *
	 * @return $this
	 */
	function editColumn($column,$callback){
		$this->list->editColumn($column,$callback);
		return $this;
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
	function render($view,$data = [], $mergeData = []){
		if($this->request->ajax() || $this->request->wantsJson()){
			$this->collection($this->query());
			if(!$this->list){
				return $this->ajaxError();
			}
			$this->process();
			if(!$this->count){
				$this->count=$this->list->count();
			}
			return $this->ajaxSuccess(['list'=>$this->list,'count'=>$this->count,'next'=>$this->next]);
		}
		return view($view,$data , $mergeData );
	}
}