<?php
namespace App\Console\Commands;
use App\Model\Role;
use App\User;
use Illuminate\Console\Command;

class Install extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'install';

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
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		if(Role::query()->first()){
			$this->line('Role installed');
		}else{
			$this->line('Installing role...');
			$this->installRole();
			$this->line('Role installed');
		}
		$this->line('Check admin');
		$admin=Role::findByName('admin');
		if(!$admin){
			$this->line('No admin role...Exit');
			return ;
		}

		if(count($admin->users)>=1){
			$this->line('Already installed....Done!');
			return ;
		}
		$this->line('No admin. Setting admin now...');
		$first=User::query()->first();
		if(!$first){
			$this->line('No user. Please login first');
			return ;
		}
		/**
		 * @var User $first
		 */
		$first->assignRole('admin');
		$first->grantSuperAdmin();
		$this->line('Install completed');
		$this->line('Admin account is: '.$first->name.':'.$first->email);

	}
	function installRole(){
		$roles=[
			'admin'=>['_all_caps'=>1],
			'customer'=>[],
			'user'=>[],
		];
		foreach ($roles as $role=>$caps) {
			Role::create(['name'=>$role,'caps'=>$caps]);
		}
	}


}