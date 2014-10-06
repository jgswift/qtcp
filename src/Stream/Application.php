<?php
namespace qtcp\Stream {
    use qtcp;
    use qtil;
    
    class Application extends qtcp\Network\Application {
        
        protected $wrappers;
        
        function __construct($resource) {
            $this->wrappers = new qtil\Collection();
            
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