<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 06-Sep-18
 * Time: 10:32 AM
 */

namespace App\Console\Commands;


use App\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature='mkadmin {user}';

    function handle(){
        $user=$this->argument('user');
        $user=User::find($user);
        if($user) {
            $user->assignRole('admin');
            $user->grantSuperAdmin();
            $this->info('Done');
        }else{
            $this->error('User not found');
        }
    }
}