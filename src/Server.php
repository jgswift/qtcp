<?php
namespace qtcp {
    use \Ratchet\MessageComponentInterface;
    use \Ratchet\ConnectionInterface;
    use observr;
    use qtil;

    class Server implements MessageComponentInterface {
        use observr\Subject;
        
        protected $clients;
        
        private $reader;
        private $application;
        private $console;
        
        public function __construct(Application $app) {
            $this->application = $app;
            $this->console = $app->getConsole();
            $this->clients = [];
            $this->reader = new Stream\Reader($app->getProtocol());
        }

        function getClients() {
            return $this->clients;
        }

        function getClientByID($id) {
            if(isset($this->clients[$id])) {
                return $this->clients[$id];
            }
        }
        
        public function onOpen(ConnectionInterface $conn) {
            // Store the new connection to send messages to later
            $this->clients[$conn->resourceId] = $client = new Client($this->application, $conn);
            
            $client->open();

            $this->console->writeLn("Connection + ({$conn->resourceId}):(".$conn->remoteAddress.") connected");
            
            $this->application->setState('connect', new observr\Event($client));
        }

        public function onMessage(ConnectionInterface $from, $msg) {
            $client = $this->clients[$from->resourceId];
            
            $this->reader->setStream($client);
            
            $packet = $this->reader->readPacket($msg);
            
            if($packet) {
                $client->refresh();
                $packet->setState('receive', new Network\Packet\Event($client,$packet));
                $this->setState($packet->getID(), new Network\Packet\Event($client,$packet));
            }

            $this->console->writeLn(sprintf('Connection | %d:(%s) sent "%s"'
                , $from->resourceId, $from->remoteAddress, qtil\ReflectorUtil::getClassName(get_class($packet))));
        }

        public function onClose(ConnectionInterface $conn) {
            // The connection is closed, remove it, as we can no longer send it messages
            $this->application->setState('disconnect', new observr\Event($this->clients[$conn->resourceId]));
            
            $this->clients[$conn->resourceId]->close();
            
            unset($this->clients[$conn->resourceId]);

            $this->console->writeLn("Connection - ({$conn->resourceId}):(".$conn->remoteAddress.") disconnected");
            
            
        }

        public function onError(ConnectionInterface $conn, \Exception $e) {
            $this->console->writeLn("An error has occurred: {$e->getMessage()}");
            $this->console->writeLn("{$e->getTraceAsString()}");

            $conn->close();
        }
    }

}