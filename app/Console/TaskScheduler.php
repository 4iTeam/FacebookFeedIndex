<?php
namespace App\Console;

use App\Model\Task;

class TaskScheduler{
    /**
     * @return Task[]
     */
    public static function allTasks(){
        return Task::forSchedule();
    }

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     */
    public static function schedule($schedule){
        foreach(static::allTasks() as $task){
            $event=$schedule->command('task:run '.$task->id)->runInBackground()
                ->after(function() use ($task){
                    $task->finish();
                });
            if($task->cron){
                if($task->result>0) {//if error
                    $event->everyMinute();//retry now
                }else{
                    $event->cron($task->cron);
                }
            }else{
                $event->everyMinute();
            }
        }

    }
}