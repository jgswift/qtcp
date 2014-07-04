<?php
namespace qtcp\Stream {
    use observr;
    
    abstract class Application extends \qtcp\Application\Base {
        
        protected $wrappers;
        
        function __construct(\qtcp\Network\Resource $resource) {
            $this->wrappers = new observr\Collection();
            
            parent::__construct($resource);
        }
        
        function getWrappers() {
            return $this->wrappers;
        }
               
        function serving($name) {
            return array_key_exists($name,$this->wrappers);
        }
    }
}