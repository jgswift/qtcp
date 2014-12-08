<?php
namespace qtcp\Application {
    use qtcp;
    
    class Clock {
        protected $timers = [];
        protected $tick = 1;
        protected $application;
        protected $speed = 0.001;
        
        /**
         * Creates an empty clock
         * @param qtcp\Application $application
         */
        function __construct(qtcp\Application $application) {
            $this->application = $application;
        }
        
        function getSpeed() {
            return $this->speed;
        }
        
        /**
         * Adds a timer to the clock
         * @param \qtcp\Application\Timer $timer
         * @return \qtcp\Application\Timer
         */
        function addTimer(Timer $timer) {
            $interval = (string)$timer->getInterval();

            if(!isset($this->timers[$interval])) {
                $this->timers[$interval] = [];
            }

            $this->timers[$interval][] = $timer;
            
            $timer->start();

            return $timer;
        }
        
        /**
         * Retrieves all timers from the clock
         * @return array
         */
        function getTimers() {
            $t = [];
            foreach($this->timers as $timers) {
                $t = array_merge($t,$timers);
            }
            
            return $t;
        }
        
        /**
         * Retrieve all timers on a specific interval
         * @param integer $i
         * @return array
         */
        function getTimersByInterval($i) {
            if(array_key_exists($i,$this->timers)) {
                return $this->timers[$i];
            }
        }
        
        /**
         * Removes timer from clock
         * @param \qtcp\Application\Timer $timer
         */
        function removeTimer(Timer $timer) {
            $i = (string)$timer->getInterval();
            
            $timers = $this->getTimersByInterval($i);
            
            if(is_array($timers)) {
                $k = array_search($timer,$timers);

                if($k !== false) {
                    $timer->stop();
                    unset($this->timers[$i][$k]);
                }
            }
        }
        
        /**
         * Removes all timers from the clock
         */
        function clearTimers() {
            foreach($this->timers as $i => $timers) {
                foreach($timers as $k => $timer) {
                    $timer->stop();
                    unset($this->timers[$i][$k]);
                }
            }
        }

        /**
         * Executes all timers in clock
         * @param array $timers
         */
        protected function executeTimers(array $timers) {
            if(!empty($timers)) {
                foreach($timers as $timer) {
                   $timer();
                }
            }
        }
        
        /**
         * Ticks all timers in clock
         */
        function tick() {
            foreach($this->timers as $interval => $timers) {
                if($this->dmod($this->tick,(float)$interval)) {
                    $this->executeTimers($timers);
                }
            }
            
            $this->tick+=$this->speed;
        }
        
        /**
         * Compute modulus
         * @param integer $x
         * @param integer $y
         * @return integer
         */
        private function dmod($x,$y) {
            return abs(($x / $y) - round($x / $y, 0)) < 0.0001;
        }
    }
}