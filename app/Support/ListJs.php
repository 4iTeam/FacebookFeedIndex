<?php
namespace App\Support;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;

class ListJs implements Jsonable, JsonSerializable {
	protected $list=[];
	protected $columns=[];
	protected $allColumns=false;
	function __construct($list,$columns='*') {
		$this->list=$list;
		$this->addColumn($columns);
	}

	/**
	 * @param null $list
	 *
	 * @return Collection|array|$this
	 */
	function data($list=null){
		if($list===null){
			return $this->list;
		}
		$this->list=$list;
		return $this;
	}
	function collection(){
		if($this->list instanceof Collection){
			return $this->list;
		}else{
			return new Collection($this->list);
		}
	}
	function addColumn($columns){
		if(!is_array($columns)){
			$columns=func_get_args();
		}
		foreach ($columns as $col){
			if($col=='*'){
				$this->allColumns=true;
				return $this;
			}
			$this->editColumn($col,function($i)use($col){
				return data_get($i,$col);
			});
		}
		return $this;
	}

	/**
	 * @param $column
	 * @param $callback
	 *
	 * @return $this
	 */
	function editColumn($column,$callback){
		$this->columns[$column]=$callback;
		return $this;
	}
	public function toJson($options = 0)
	{
		return json_encode($this->jsonSerialize(), $options);
	}
	function jsonSerialize(){
		return $this->toArray();
	}
	function toArray(){
		$response=[];
		foreach ($this->list as $item){
			$the_item=$this->allColumns?(array)$item:[];
			foreach ($this->columns as $col=>$func){
				$the_item[$col]=$func($item);
			}
			$response[]=$the_item;
		}
		return $response;
	}
	function count(){
		return count($this->list);
	}
}