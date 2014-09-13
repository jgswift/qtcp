<?php
namespace qtcp\Stream {
    use qtcp;
    use qio;
    
    class Writer extends qio\Stream\Writer {
        public function write($data) {
            $this->getSocket()->send((string)$data);
        }
        
        public function writePacket(qtcp\Network\Packet $packet, $data = null) {
            if(is_array($data)) {
                $packet->setData($data);
            }

            $this->write($packet);
        }
        
        public function getSocket() {
            return $this->getStream()->getSocket();
        }
    }
}