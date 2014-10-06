<?php
namespace qtcp\Network {
    use observr;
    use qtil;
    use observr\Subject\SubjectInterface;
    
    class Packet implements \ArrayAccess, SubjectInterface {
        use observr\Subject, qtil\Reflector;
        
        public $id;
        public $data = [];
        
        /**
         * 
         * @param string $id
         * @param array $data
         */
        public function __construct($id = null, $data=null) {
            if(is_array($id)) {
                $data = $id;
                $id = null;
            }
            
            if(is_null($id)) {
                $this->getID();
            } else {
                $this->id = $id;
            }
            
            $this->data = $data;
        }
        
        public function getData() {
            return $this->data;
        }
        
        public function setData($data) {
            return $this->data = $data;
        }
        
        public function getID() {
            if(!isset($this->id)) {
                $this->id = strtolower(qtil\ReflectorUtil::getClassName(get_called_class()));
            }
            
            return $this->id;
        }
        
        public function __toString() {
            return \qtil\JSONUtil::encode(['id'=>$this->getID(),'data'=>$this->data]);
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