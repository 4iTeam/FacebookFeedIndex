<?php
namespace App\Model\Traits\User;
trait UserAvatarTrait{
    function getAvatar($args=[]){
        if($this->facebook_id){
            return $this->getFacebookAvatar($args);
        }
        return get_gravatar($this->email,$args);
    }

    protected function getFacebookAvatar($args){
        $args=array_merge([
            'alt'=>$this->name,
            'width'=>null,
            'height'=>null,
            'extra_attr'=>'',
            'class'=>'',
        ],$args);
        $class=['avatar'];
        if ( $args['class'] ) {
            if ( is_array( $args['class'] ) ) {
                $class = array_merge( $class, $args['class'] );
            } else {
                $class[] = $args['class'];
            }
        }
        $url=$this->getFacebookAvatarUrl($args);
        $avatar = sprintf(
            "<img alt='%s' src='%s' class='%s' %s/>",
            esc_attr( $args['alt'] ),
            esc_url( $url ),
            esc_attr( join( ' ', $class ) ),
            $args['extra_attr']
        );
        return $avatar;
    }
    protected function getFacebookAvatarUrl($args){
        $args=array_merge(['type'=>'normal'],$args);
        if(isset($args['size']) && (($size=absint($args['size']))>0)){
            $args['width']=$args['height']=$size;
        }
        $args=array_only($args,['type','size']);

        $src='https://graph.facebook.com/%s/picture';
        $src=sprintf($src,$this->facebook_id);
        $src=add_query_arg($args,$src);
        return $src;
    }

    protected function getAvatarUrlAttribute(){
        return get_gravatar_url($this->email);
    }
    protected function getAvatarAttribute(){
        return $this->getAvatar(['class'=>'img-circle']);
    }
}