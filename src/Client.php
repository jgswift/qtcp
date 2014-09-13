<?php
namespace qtcp {
    use observr;
    use qtil;
    use qio;
    use \Ratchet\ConnectionInterface;
    
    class Client implements qio\Stream {
        use observr\Subject;
        
        protected $socket;
        protected $character;
        protected $id;
        protected $idle = 0;
        protected $context;
        protected $pointer;
        protected $application;
        protected $timers;
        protected $writer;
        protected $open = false;
        
        function __construct(Application $app, ConnectionInterface $socket) {
            $this->application = $app;
            $this->socket = $socket;
            $this->id = $socket->resourceId;
            
            $this->timers = new qtil\Collection;
            $this->writer = new Stream\Writer($this);
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
        
        function open() { 
            $this->open = true;
        }
        
        function close() {
            if($this->open) {
                $this->open = false;
                $this->socket->close();
            }
        }
        
        function isOpen() {
            return $this->open;
        }
        
        function idle() {
            $this->idle+=$this->application->clock->getSpeed();
        }
        
        function refresh() {
            $this->idle = 0;
        }
        
        function send($packet,$data=null) {
            if($this->isOpen()) {
                if($packet instanceof Network\Packet) {
                    $e = new Network\Packet\Event($this, $packet);
                    $packet->setState('send', $e);
                    if(!$e->canceled) {
                        $this->writer->writePacket($packet, $data);
                    }
                } elseif(is_string($packet)) {
                    $this->writer->write($packet);
                }
            }
        }
    }
}