<?php
namespace App\Model;
use Closure;
use Symfony\Component\Console\Output\Output;
class TaskBufferedOutput extends Output {
    /**
     * @var string
     */
    private $buffer = '';
    /**
     * @var Closure
     */
    private $onBufferCallback=null;

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        $content = $this->buffer;
        $this->buffer = '';

        return $content;
    }
    public function onBuffer(\Closure $callback){
        $this->onBufferCallback=$callback;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= PHP_EOL;
        }
        if($this->onBufferCallback instanceof Closure){
            $this->onBufferCallback->__invoke($this->buffer);
        }
    }
}