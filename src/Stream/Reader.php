<?php
namespace qtcp\Stream {
    use qtcp;
    use qtil;
    
    class Reader extends \qio\Stream\Reader {
        
        private $builder;
        
        function __construct(qtcp\Network\Packet\Builder $builder, $streamOrWrapper=null) {
            parent::__construct($streamOrWrapper);
            $this->builder = $builder;
        }
        
        public function read($length = null) { } // NOT IMPLEMENTED, RATCHET STREAM HANDLES SOCKET READING

        public function readPacket($json) {
            $json_data = \qtil\JSONUtil::decode($json);
            //TODO: VALIDATE JSON SCHEMA
            if(is_object($json_data) && isset($json_data->id)) {
                $data = [];
                if(isset($json_data->data)) {
                    $data = $json_data->data;
                }
                
                $packet = false;
                try {
                    $packet = $this->builder->link($json_data->id,[$json_data->id,$data]);
                    $packet->id = $json_data->id;
                    $packet->data = $data;
                } catch(\BadMethodCallException $e) {
                    $packet = new qtcp\Network\Packet($json_data->id,(array)$data);
                }
                
                return $packet;
            }
            
            return false;
        }
        
        public function getBuilder() {
            return $this->builder;
        }
    }
}