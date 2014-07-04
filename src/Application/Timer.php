<?php
namespace qtcp\Application {
    use qtil, qtcp;
    
    class Timer implements qtcp\Timer {
        use qtil\Executable;
        
        protected $interval;
        protected $callback;
        protected $enabled;
        
        function __construct($interval, callable $callback) {
            $this->setInterval($interval);            
            $this->callback = $callback;
        }
        
        function getInterval() {
            return $this->interval;
        }

        function setInterval($interval) {
            if(is_numeric($interval)) {
                $this->interval = $interval;
            } else {
                throw new \InvalidArgumentException;
            }
        }
        
        function getCallback() {
            return $this->callback;
        }
        
        function setCallback(callable $callback) {
            return $this->callback = $callback;
        }
        
        function stop() {
            $this->enabled = false;
        }
        
        function start() {
            $this->enabled = true;
        }
        
        function execute() {
            if(!$this->enabled) { return; }
            if($this->callback instanceof \Closure) {
                @$this->callback->bindTo($this,$this);
            }
            return call_user_func_array($this->callback, func_get_args());
        }
    }
}