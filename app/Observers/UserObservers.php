<?php
namespace App\Observers;
use App\User;

class UserObservers{
    function saving(User $user){
        if($user->group_id && $user->page_id){
            $changes=$user->getDirty();
            if(isset($changes['group_id'])){
                $user->page_id='';
            }else{
                $user->group_id='';
            }
        }
    }
    function deleting(User $user){

    }
}