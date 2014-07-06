<?php
namespace qtcp {
    use observr;
    use qtil;
    use qio;
    use Ratchet\ConnectionInterface;
    
    class Client implements qio\Stream, \ArrayAccess {
        use qtil\ArrayAccess;
        
        protected $socket;
        protected $character;
        protected $id;
        protected $idle = 0;
        protected $context;
        protected $pointer;
        protected $application;
        protected $timers;
        
        function __construct(Application $app, ConnectionInterface $socket) {
            $this->application = $app;
            $this->socket = $socket;
            $this->id = $socket->resourceId;
            
            $this->timers = new qtil\Collection;
        }
        
        function getID() {
            return $this->id;
        }
        
        function getApplication() {
            return $this->application;
        }
        
        function getTimers() {
            return $this->timers;
        }
        
        function getPath() {
            return $this->getID();
        }
        
        function getContext() {
            return $this->context;
        }
        
        function getSocket() {
            return $this->socket;
        }
        
        function getIdleSeconds() {
            return $this->idle;
        }
        
        function getPointer() {
            return $this->socket;
        }
        
        function getDefaultEncoding() {
            return \qio\Stream\Encoding::UTF_8;
        }
        
        function setContext(\qio\Context $context) {
            $this->context = $context;
        }
        
        function setPointer($resource) { }
        
        function isIdle() {
            return ($this->idle > 900) ? true : false;
        }
        
        function open() { }
        
        function close() {
            $this->socket->close();
        }
        
        function isOpen() {
            return true;
        }
        
        function idle() {
            $this->idle+=0.01;
        }
        
        function refresh() {
            $this->idle = 0;
        }
        
        function send($packet,$data=null) {
            if($packet instanceof Network\Packet) {
                if(is_array($data)) {
                    $packet->setData($data);
                }

                $e = new observr\Event($this);
                $packet->setState('send', $e);

                if(!$e->canceled) {
                    $this->socket->send((string)$packet);
                }
            } elseif(is_string($packet)) {
                $this->socket->send($packet);
            }
        }
    }
}