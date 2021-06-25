<?php
namespace App\Exceptions;

use Illuminate\Support\Arr;
use RuntimeException;
class InvalidStatusException extends RuntimeException{
    function __construct($message = "", $code = 0, \Exception $previous = null) {
        $this->message='Invalid user status';
    }

    protected $model;

    public function setModel($model,$validStatuses=[])
    {
        $this->model = $model;
        $modelClass=get_class($model);
        if(!$validStatuses && method_exists($model,'allStatuses')) {
            $validStatuses = $model->allStatuses();
            $validStatuses = $validStatuses->pluck('status')->all();
        }
        $validStatuses=join(', ',$validStatuses);

        $this->message = "Invalid status $modelClass [{$model->status}]. All valid statuses are [$validStatuses]";

        return $this;
    }

    /**
     * Get the affected Eloquent model.
     *
     * @return object
     */
    public function getModel()
    {
        return $this->model;
    }
}