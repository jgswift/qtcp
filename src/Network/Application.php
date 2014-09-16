<?php
namespace qtcp\Network {
    use qtcp;
    use observr;
    use Symfony\Component\Console\Output;
    
    class Application implements qtcp\Application {
        use observr\Subject;

        protected $provider;
        
        protected $resource;
        protected $console;
        protected $protocol;
        
        public $clock;
        
        function __construct($resource) {
            $this->resource = $this->parseResource($resource);
            $this->console = new Output\ConsoleOutput();
            $this->clock = new qtcp\Application\Clock($this);
            $this->provider = new qtcp\Network\Provider($this);

            if(method_exists($this,'initialize')) {
                $this->initialize();
            }
        }
        
        protected function parseResource($resource) {
            if(is_string($resource)) {
                $port = null;
                if(strpos($resource,':') === false) {
                    $address = $resource;
                } else {
                    list($address,$port) = explode(':',$resource);
                }
                
                $resource = new qtcp\Network\Resource($address, $port);
            } elseif(is_array($resource) && !empty($resource)) {
                $address = array_shift($resource);
                $port = null;
                if(!empty($resource)) {
                    $port = array_shift($resource);
                }
                
                $resource = new qtcp\Network\Resource($address, $port);
            }
            
            if(!($resource instanceof qtcp\Network\Resource)) {
                throw new \InvalidArgumentException;
            }
            
            return $resource;
        }
        
        public function getProtocol() {
            if(isset($this->protocol)) {
                return $this->protocol;
            }
            
            return $this->protocol = new qtcp\Network\Protocol();
        }
        
        function getClock() {
            return $this->clock;
        }
        
        function getResource() {
            return $this->resource;
        }
        
        function getConsole() {
            return $this->console;
        }

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