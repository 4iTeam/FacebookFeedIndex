<?php
namespace App\Model\Traits;
use App\User;
use App\Model\Role;

/**
 * Trait RolePermissionTrait
 * @package App\Model\Traits
 * @property $role_name
 */
trait RolePermissionTrait{

    private $the_role;
    private $allcaps;
    static $private_caps=['super_admin','edit_roles'];
    function assignRole($roleId = null,$create=false){
    	$role=null;
		if($roleId instanceof Role){
			$role=$roleId;
		}elseif(is_numeric($roleId)){
			$role=Role::find($roleId);
		}elseif (is_string($roleId)){
			$role=Role::where('name',$roleId)->first();
			if(!$role&&$create){
				$role=Role::create(['name'=>$roleId,'caps'=>'']);
			}
		}
		if($role){
			$this->role_id=$role->id;
			$this->save();
		}

    }
    function role(){
        return $this->belongsTo(Role::class);
    }
	/**
	 * @param $fresh
	 * @return Role|null
	 */
    function getRole($fresh=true){
    	return $this->role;
    }


    protected function get_allcaps($reload=false){
        if(is_null($this->allcaps)){
            $reload=true;
        }
        if($reload){
            $this->allcaps=[];
	        $role_caps=[];
            if($role=$this->getRole()) {
	            $role_caps =$role->caps;
	            $role_caps[$role->name]=1;
            }
            $userCaps=$this->meta('capabilities');
            if(!is_array($userCaps)) $userCaps=array();
            $this->allcaps=array_merge($role_caps,$userCaps);
        }
        return $this->allcaps;
    }
    function has_cap($cap){
    	$cap=trim($cap,'_');
        if($this->isSuperAdmin()){//super admin has all caps
            return true;
        }
		if(in_array($cap,static::$private_caps)){
            return $this->has_private_cap($cap);
		}
        $caps=$this->get_allcaps();
		if(!empty($caps['_all_caps'])){
			return true;
		}
        return !empty($caps[$cap]);
    }
    protected function has_private_cap($cap){
        $userCaps=$this->meta('capabilities');
        return !empty($userCaps[$cap]);
    }
    public function add_cap( $cap, $grant = true ) {
        $caps=$this->meta('capabilities');
        if(!is_array($caps))$caps=array();
        $caps[$cap] = $grant;
        $this->meta('capabilities',$caps);
    }
    public function remove_cap( $cap ) {
        $caps=$this->meta('capabilities');
        if(!is_array($caps)){
            $caps=array();
        }
        if ( ! isset( $caps[ $cap ] ) ) {
            return;
        }
        unset( $caps[ $cap ] );
        $this->meta('capabilities',$caps);
    }
    function grantSuperAdmin(){
        $this->add_cap('super_admin');
        return $this;
    }
    function revokeSuperAdmin(){
        $this->remove_cap('super_admin');
        return $this;
    }
    function isSuperAdmin(){
        return $this->has_private_cap('super_admin');
    }
	/**
	 * Magic __call method to handle dynamic methods.
	 *
	 * @param  string $method
	 * @param  array  $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments = array())
	{
		// Handle isRoleslug() methods
		if (starts_with($method, 'is') and $method !== 'is') {
			$role = substr($method, 2);
			$role = snake_case($role);
			return $role==$this->role_name;
		}

		return parent::__call($method, $arguments);
	}
	public function isRole($role){
		return $role==$this->role_name;
	}
    protected function getRoleNameAttribute(){
        if($role=$this->getRole()){
            return $role->name;
        }
        return '';
    }
}