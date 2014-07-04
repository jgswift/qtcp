<?php
namespace qtcp\Network {
    use qio;
    
    class Resource implements qio\Resource {
        private $path;
        private $port;
        
        function __construct($address,$port=null) {
            if(is_null($port)) {
                $port = 8080;
            }
            
            $this->path = $address;
            $this->port = $port;
        }
        
        public function getDefaultMode() {
            return qio\Stream\Mode::ReadWrite;
        }
        
        public function getAddress() {
            return $this->getPath();
        }

        public function getPath() {
            return $this->path;
        }

        public function getPort() {
            return $this->port;
        }
    }
}