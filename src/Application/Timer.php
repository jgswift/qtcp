<?php
namespace qtcp\Application {
    use qtil, qtcp;
    
    class Timer implements qtcp\Timer {
        use qtil\Executable;
        
        protected $interval;
        protected $callback;
        protected $enabled;
        protected $clock;

        public function __construct($interval, callable $callback) {
            $this->setInterval($interval);
            $this->setCallback($callback);
        }
        
        public function getClock() {
            return $this->clock;
        }
        
        public function setClock(BubblerClock $clock) {
            return $this->clock = $clock;
        }

        public function getInterval() {
            return $this->interval;
        }

        public function setInterval($interval) {
            if(is_numeric($interval)) {
                $this->interval = $interval;
            } else {
                throw new \InvalidArgumentException("Interval must be a numeric integer or float - (".gettype($interval).")");
            }
        }

        public function getCallback() {
            return $this->callback;
        }

        public function setCallback(callable $callback) {
            return $this->callback = $callback;
        }

        public function stop() {
            $this->enabled = false;
        }

        public function start() {
            $this->enabled = true;
        }

        public function execute() {
            if(!$this->enabled) { return; }
            if($this->callback instanceof \Closure) {
                @$this->callback->bindTo($this,$this);
            }
            return call_user_func_array($this->callback, func_get_args());
        }
    }
}