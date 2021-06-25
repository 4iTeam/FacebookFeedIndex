<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 25-Dec-17
 * Time: 11:53 AM
 */

namespace App\Policies;


class RolePolicy
{
    function __call($name, $arguments)
    {
        return $arguments[0]->can('edit_roles');
    }
}