<?php
namespace qtcp\Stream {
    use qtcp;
    use qio;
    
    class Writer extends qio\Stream\Writer {
        public function write($data) {
            if($data instanceof qtcp\Network\Packet) {
                $this->writePacket($data);
            } else {
                $this->getSocket()->send((string)$data);
            }
        }
        
        public function writePacket(qtcp\Network\Packet $packet, $data = null) {
            if(is_array($data)) {
                $packet->setData($data);
            }

            $e = new qtcp\Network\Packet\Event($this, $packet);
            $packet->setState('send', $e);
            $this->setState($packet->getID(), $e);

            if(!$e->canceled) {
                $this->write($packet);
            }
        }
        
        public function getSocket() {
            return $this->getStream()->getSocket();
        }
    }
}