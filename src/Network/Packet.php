<?php
namespace qtcp\Network {
    use observr;
    use qtil;
    
    abstract class Packet implements \ArrayAccess {
        use observr\Subject, qtil\Reflector;
        
        public $id;
        public $data = [];
        
        public function __construct($data=null) {
            if(is_array($data)) {
                $this->data = $data;
            }
            
            $this->id = $this->getID();
        }
        
        public function getData() {
            return $this->data;
        }
        
        public function setData($data) {
            return $this->data = $data;
        }
        
        public function getID() {
            return strtolower(qtil\ReflectorUtil::getClassName(get_called_class()));
        }
        
        public function __toString() {
            return \qtil\JSONUtil::encode(['id'=>$this->id,'data'=>$this->data]);
        }

        public function offsetExists($offset) {
            return isset($this->data[$offset]);
        }

        public function &offsetGet($offset) {
            return $this->data[$offset];
        }

        public function offsetSet($offset, $value) {
            $this->data[$offset] = $value;
        }

        public function offsetUnset($offset) {
            unset($this->data[$offset]);
        }

    }
}