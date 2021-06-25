<?php
namespace App\Sites\Modules\Managers;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;

class SiteModuleManager extends Manager {
    protected $modules=[

    ];
    function __construct(\Illuminate\Foundation\Application $app)
    {
        parent::__construct($app);
        foreach ($this->modules as $module=>$class) {
            $this->extend($module, function ($app) use($class) {
                return $app->make($class);
            });
        }
    }

    public function getDefaultDriver()
    {

    }

    function hasDriver($driver){
        $method = 'create'.Str::studly($driver).'Driver';
        return isset($this->customCreators[$driver])||method_exists($this, $method);
    }
}