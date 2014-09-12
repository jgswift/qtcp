<?php
namespace qtcp\Network\Packet {
    use observr;
    use qtcp;
    
    class Event extends observr\Event {
        function __construct(qtcp\Client $client, qtcp\Network\Packet $packet) {
            parent::__construct($client, ['packet'=>$packet,'data'=>$packet->data]);
        }
        
        function getClient() {
            return $this->sender;
        }
        
        function getPacket() {
            return $this->packet;
        }
    }
}