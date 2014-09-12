<?php
namespace qtcp\Stream {
    use qtcp;
    
    class Reader extends \qio\Stream\Reader {
        
        private $builder;
        
        function __construct(qtcp\Network\Packet\Builder $builder, $streamOrWrapper=null) {
            parent::__construct($streamOrWrapper);
            $this->builder = $builder;
        }
        
        public function read($length = null) { } // NOT IMPLEMENTED, RATCHET STREAM HANDLES SOCKET READING

        public function readPacket($json) {
            $json_data = \qtil\JSONUtil::decode($json);
            if(is_object($json_data) && isset($json_data->id)) {
                $packet = $this->builder->link($json_data->id,[]);

                $data = [];
                if(is_object($json_data) && isset($json_data->data)) {
                    $data = $json_data->data;
                }

                $packet->setData($data);
                
                return $packet;
            }
            
            return false;
        }
        
        public function getBuilder() {
            return $this->builder;
        }
    }
}