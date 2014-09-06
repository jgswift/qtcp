<?php
namespace qtcp\Application {
    use qtcp;
    use observr;
    use Symfony\Component\Console\Output;
    
    abstract class Base implements \qtcp\Application {
        use observr\Subject;

        protected $provider;
        
        protected $resource;
        protected $console;
        
        public $clock;
        
        function __construct(qtcp\Network\Resource $resource) {
            $this->resource = $resource;
            $this->console = new Output\ConsoleOutput();
            $this->clock = new Clock($this);
            $this->provider = new \qtcp\Network\Provider($this);

            $this->initialize();
        }
        
        abstract function getProtocol();
        
        function getClock() {
            return $this->clock;
        }
        
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
        
        function __destruct() {
            $clients = $this->application->getServer()->getClients();

            if(!empty($clients)) {
                foreach($clients as $client) {
                    $client->close();
                }
            }
        }
    }
}