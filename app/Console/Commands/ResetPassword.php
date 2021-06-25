<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 28-Feb-18
 * Time: 10:23 AM
 */

namespace App\Console\Commands;


use App\User;
use Illuminate\Console\Command;

class ResetPassword extends Command
{
    protected $signature = 'passwd {user}';
    function handle(){
        $user=User::find($this->argument('user'));
        if(!$user){
            $this->error('User not found');
            return ;
        }
        $newPass=$this->ask('Enter new password');
        if($newPass){
            $user->password=bcrypt($newPass);
            $user->save();
            $this->info('Password reset successfully');
        }

    }
}