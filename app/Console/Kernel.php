<?php

namespace App\Console;

use App\Console\Commands\Install;
use App\Console\Commands\MakeAdmin;
use App\Console\Commands\ResetPassword;
use App\Console\Commands\ScheduleStatus;
use App\Console\Commands\Task;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Install::class,
        Task::class,
        ScheduleStatus::class,
        ResetPassword::class,
        MakeAdmin::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->events->listen(CommandStarting::class,function(CommandStarting $event){
            $this->app->instance('command_output',$event->output);
        });
        if($this->skipSchedule()){
            return ;
        }
        $schedule->command('facebook:index')->withoutOverlapping()->everyTenMinutes();
    }
    function skipSchedule(){
        return skipSchedule();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        //require base_path('routes/console.php');
        $this->load(__DIR__.'/Commands');
    }
}
