<?php
namespace App\Model;
use App\Model;
use Carbon\Carbon;
use \Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use \Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Task
 * @package App\Model
 * @property $id
 * @property $command
 * @property $status
 * @property $output
 * @property $started_at
 * @property $ended_at
 * @property $cron
 * @property $result
 * @property $retry
 * @property $max_retry
 * @property $request
 */
class Task extends Model{
    protected $fillable=['command','status','cron','desc','max_retry','request'];
    protected $dates=['started_at','ended_at'];
    public $timestamps=false;
    static function boot()
    {
        parent::boot();
        static::saving(function(Task $model){
            if($model->cron===null){
                $model->cron='';
            }
        });
    }

    public function run(){
        if(!$this->valid()){
            return;
        }
        $this->status='running';
        $this->started_at=Carbon::now();
        $this->ended_at=null;
        $this->output='';
        $this->save();
        $kernel = app()->make(Kernel::class);
        /**
         * @var \App\Console\Kernel $kernel
         */
        $input=array_merge(['artisan'],explode(' ',$this->command));
        $input=new ArgvInput($input);
        $output= new TaskBufferedOutput();
        $output->onBuffer(function($res){
            $this->output=$res;
            $this->save();
        });
        $this->result=$kernel->handle($input,$output);
        $this->output=$output->fetch();
        $this->ended_at=Carbon::now();
        $this->save();
    }
    public static function forSchedule(){
        return Task::query()->whereNotIn('status',['new','stopped'])->get();
    }
    protected function valid(){
        if(!$this->command){//no command to run
            return false;
        }
        if(in_array($this->status,['new','running','stopped'])){
            return false;
        }
        if($this->retry>=$this->max_retry){
            return false;
        }
        return true;
    }
    public function finish(){
        $this->ended_at=Carbon::now();
        if($this->result>0){
            $this->retry++;
        }else{
            $this->retry=0;
        }
        if($this->retry<$this->max_retry){
            if($this->cron) {
                $this->status = 'finished';
            }else{
                if($this->result>0) {
                    $this->status = 'ready';//retry if error
                }else{
                    $this->status = 'stopped';//stop if success
                }
            }
        }else{
            $this->status = 'stopped';
        }
        if($this->request==='stop'){//request task to stop
            $this->status = 'stopped';
            $this->request='';
        }
        $this->save();
    }
    public function start(){
        if($this->running()){
            return ;
        }
        $this->status='ready';
        $this->started_at=null;
        $this->output=null;
        $this->ended_at=null;
        $this->retry=0;
        $this->save();
    }
    public function stop(){
        if($this->running()) {
            $this->request = 'stop';
        }else{
            $this->status = 'stopped';
        }
        $this->save();
    }
    public function running(){
        return $this->status==='running';
    }
}