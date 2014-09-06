<?php
namespace qtcp\Application {
    use qtil;
    
    abstract class Queue extends \SplQueue {
        
        private $id;
        
        function __construct($id = null) {
            if(is_null($id)) {
                $this->id = qtil\Identifier::identify($this);
            } else {
                $this->id = $id;
                $this->load();
            }
            
            $this->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);
        }
        
        function getID() {
            return $this->id;
        }
        
        function clear() {
            if(count($this) > 0) {
                $mode = $this->getIteratorMode();
                $this->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);
                foreach($this as $item) { };
                $this->setIteratorMode($mode);
            }
        }
        
        abstract protected function save();
        
        abstract protected function load();
        
        function push($value) {
            parent::push($value);
            $this->save();
            return $value;
        }
        
        function pop() {
            $value = parent::pop();
            $this->save();
            return $value;
        }
    }
}