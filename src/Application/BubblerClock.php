<?php
namespace qtcp\Application {
    use qtcp, bubblr;
    
    class BubblerClock extends Clock {
        private $bubbler;
        
        /**
         * Creates an empty clock
         * @param qtcp\Application $application
         */
        function __construct(qtcp\Application $application) {
            parent::__construct($application);
            $this->bubbler = new bubblr\Bubbler\AggregateBubbler;
        }
        
        public function getBubbler() {
            return $this->bubbler;
        }
                
        /**
         * Executes all timers in clock
         * @param array $timers
         */
        protected function executeTimers(array $timers) {
            if(!empty($timers)) {
                foreach($timers as $timer) {
                    $timer->setClock($this);
                    $bubble = $timer();
                    $this->bubbler->getSpout()->push($bubble);
                }
            }
        }
        
        /**
         * Ticks all timers in clock
         */
        public function tick() {
            foreach($this->timers as $interval => $timers) {
                if($this->dmod($this->tick,(float)$interval)) {
                    $this->executeTimers($timers);
                }
            }

            $this->bubbler->execute();
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