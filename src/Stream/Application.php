<?php
namespace qtcp\Stream {
    use qtcp;
    use observr;
    
    class Application extends qtcp\Network\Application {
        
        protected $wrappers;
        
        function __construct($resource) {
            $this->wrappers = new observr\Collection();
            
            parent::__construct($resource);
        }
        
        public function initialize() { }
        
        function getWrappers() {
            return $this->wrappers;
        }
               
        function serving($name) {
            return array_key_exists($name,$this->wrappers);
        }
    }
}