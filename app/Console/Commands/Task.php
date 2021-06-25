<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \App\Model\Task as TaskModel;
class Task extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:run {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id=$this->argument('id');
        $task=TaskModel::find($id);
        //$task=new TaskModel(['command'=>'rank 1']);
        if($task){
            $task->run();
        }else{
            $this->error('task not found');
        }

    }
}
