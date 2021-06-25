<?php
namespace App\Support;
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 29-Dec-17
 * Time: 9:37 AM
 */
use Illuminate\Support\Facades\File;

class Helper
{
    /**
     * Load module's helpers
     */
    public static function autoload($directory)
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }


}