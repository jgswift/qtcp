<?php
namespace qtcp\Stream {
    class Writer extends \qio\Stream\Writer {
        public function write($data) {
            $this->getStream()->getSocket()->send($data);
        }
        
        public function writePacket(\qtcp\Network\Packet $packet, $data = null) {
            if(is_array($data)) {
                $packet->setData($data);
            }

            $e = new \qtcp\Network\Packet\Event($this);
            $packet->setState('send', $e);

            if(!$e->canceled) {
                $this->socket->send((string)$packet);
            }
        }
    }
}