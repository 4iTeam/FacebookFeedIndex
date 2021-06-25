<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User  $current
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $current, User $user)
    {
        return $current->can('view_user');
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $current
     * @return mixed
     */
    public function create(User $current)
    {
        return $current->can('create_user');
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $current
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $current, User $user)
    {
        if($user->isSuperAdmin()){//only super admin can delete other superadmin
            return $current->isSuperAdmin();
        }
        return $current->can('update_user');
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $current
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $current, User $user)
    {
        if($user->id==$current->id){
        	return false;
        }
    	if($user->isSuperAdmin()){//only super admin can delete other superadmin
        	return $current->isSuperAdmin();
        }
        return $current->can('delete_user');
    }

    function super_admin(User $current, User $user){
        if($current->id==$user->id){
            return false;
        }
        return $current->isSuperAdmin() && $user->isRole('admin');
    }
    function assign_roles(User $current, User $user){
        if($current->id==$user->id){
            return false;
        }
        return $current->can('assign_roles');
    }

}
