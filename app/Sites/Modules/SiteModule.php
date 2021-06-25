<?php
namespace App\Sites\Modules;
abstract class SiteModule{
    protected $template;
    function setTemplate($template){
        $this->template=$template;
        return $this;
    }
    abstract function getData();
    function render(){
        return $this->view()->render();
    }
    function view(){
        return view($this->template,$this->getData());
    }
    function __toString()
    {
        return $this->render();
    }
}