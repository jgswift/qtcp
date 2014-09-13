<?php
namespace qtcp\Network\Packet {
    use observr;
    use qtcp;
    
    class Event extends observr\Event {
        public function __construct($sender, qtcp\Network\Packet $packet) {
            parent::__construct($sender, ['packet'=>$packet,'data'=>$packet->getData(),'id'=>$packet->getID()]);
        }
        
        public function getID() {
            return $this['id'];
        }
        
        public function getPacket() {
            return $this['packet'];
        }
    }
}