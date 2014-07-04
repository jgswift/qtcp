<?php
namespace qtcp\Application {
    use qtcp;
    use observr;
    use Symfony\Component\Console\Output;
    
    abstract class Base implements \qtcp\Application {
        use observr\Subject;

        protected $timers = [];

        private $tick = 1;
        protected $provider;
        
        protected $resource;
        protected $console;
        
        function __construct(qtcp\Network\Resource $resource) {
            $this->resource = $resource;
            $this->console = new Output\ConsoleOutput();
            $this->provider = new \qtcp\Network\Provider($this);

            $this->initialize();
        }
        
        abstract function getProtocol();
        
        function getResource() {
            return $this->resource;
        }
        
        function getConsole() {
            return $this->console;
        }

        abstract function initialize();

        public function getServer() {
            return $this->provider->getServer();
        }

        public function run() {
            $this->console->writeLn("Starting server..");
            $this->setState('start', $e = new observr\Event($this));

            if($e->canceled) {
                $message = (isset($e->message)) ? $e->message : 'Unknown reason';
                $this->console->writeLn('Server start failed.. "'.$message.'"');
            } else {
                $this->console->writeLn('Server started.');
                $this->provider->getIO()->run();
                $this->console->writeLn('Shutting down..');
            }
        }

        function addTimer(Timer $timer) {
            $interval = (string)$timer->getInterval();

            if(!isset($this->timers[$interval])) {
                $this->timers[$interval] = [];
            }

            $this->timers[$interval][] = $timer;
            
            $timer->start();

            return $timer;
        }
        
        function getTimers() {
            $t = [];
            foreach($this->timers as $timers) {
                $t = array_merge($t,$timers);
            }
            
            return $t;
        }
        
        function getTimersByInterval($i) {
            if(array_key_exists($i,$this->timers)) {
                return $this->timers[$i];
            }
        }
        
        function removeTimer(Timer $timer) {
            $i = (string)$timer->getInterval();
            
            $timers = $this->getTimersByInterval($i);
            
            if(is_array($timers)) {
                $k = array_search($timer,$timers);

                if($k !== false) {
                    $timer->stop();
                    unset($this->timers[$i][$k]);
                }
            } else {
                var_dump($i);
            }
        }
        
        function clearTimers() {
            foreach($this->timers as $i => $timers) {
                foreach($timers as $k => $timer) {
                    $timer->stop();
                    unset($this->timers[$i][$k]);
                }
            }
        }

        protected function executeTimers(array $timers) {
            if(!empty($timers)) {
                foreach($timers as $timer) {
                   $timer();
                }
            }
        }
        
        private function dmod($x,$y) {
            
            return abs(($x / $y) - round($x / $y, 0)) < 0.0001;
        }

        function tick() {
            foreach($this->timers as $interval => $timers) {
                if($this->dmod($this->tick,(float)$interval)) {
                    $this->executeTimers($timers);
                }
            }

            $clients = $this->getServer()->getClients();

            // INCREASE IDLE TIME ON ALL CLIENTS
            if(!empty($clients)) {
                foreach($clients as $client) {
                    $client->idle();

                    //KICK CLIENTS IDLING OVER 15 MINUTES
                    if($client->isIdle()) {
                        $client->close();
                    }
                }
            }
            
            $this->tick+=0.1;
        }
        
        function __destruct() {
            $clients = $this->getServer()->getClients();

            if(!empty($clients)) {
                foreach($clients as $client) {
                    $client->close();
                }
            }
        }
    }
}