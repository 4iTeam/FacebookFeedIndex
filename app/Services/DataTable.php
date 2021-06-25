<?php
namespace App\Services;
use Yajra\DataTables\Services\DataTable as BaseTable;
abstract class DataTable extends BaseTable{
    function timeago($value=null){
        if(func_num_args()>0){
            return sprintf('<time class="timeago" title="%1$s" datetime="%1$s"></time>',$value);
        }
        return sprintf("'<time class=\"timeago\" datetime=\"'+data+'\" title=\"'+data+'\">'+data+'</a>'");
    }
    function makeLink($href, $attr=[] , $class=''){
        if(strpos($href,'//')===false){
            $href=admin_url($href);
        }
        $href=preg_replace_callback('/\{([^\{]*)\}/',function($matches){
            return "'+full.".$matches[1]."+'";
        },$href);

        if(!is_array($attr)){
            if($attr) {
                $attr = [ 'target' =>'_blank' ];
            }else{
                $attr =[];
            }
        }
        if($class){
            $attr['class']=$class;
        }

        $attr['href']=$href;
        if(!isset($attr['title']))
            $attr['title']="%s";

        $attr['title']=sprintf($attr['title'],"'+data+'");
        $text='%s';
        if(isset($attr['text'])){
            $text=$attr['text'];
            unset($attr['text']);
        }
        $text=sprintf($text,"'+data+'");
        foreach (['before','after','prefix','suffix'] as $var){
            if(isset($attr[$var])){
                ${$var}=$attr[$var];
                unset($attr[$var]);
            }
        }


        $attr_str=[];
        foreach ($attr as $k=>$value){
            $attr_str[]=sprintf('%s="%s"',$k,$value);
        }
        $attr_str=join(' ',$attr_str);

        $before=$after=$prefix=$suffix='';


        return sprintf("'%s<a %s>%s</a>%s'",$before,$attr_str,$text,$after);
    }
}