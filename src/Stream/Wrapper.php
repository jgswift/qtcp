<?php
namespace qtcp\Stream {
    use qtil;
    use qio;
    
    final class Wrapper implements qtil\Interfaces\Nameable {
        
        private $resource;
        private $name;
        private $id;
        
        private $stream;
        private $reader;
        private $writer;
        
        function __construct($name, qio\Resource $resource) {
            $this->name = $name;
            $this->resource = $resource;
            
            $this->id = qtil\Identifier::identify($this);
        }
        
        function getName() {
            return $this->name;
        }
        
        function getID() {
            return $this->id;
        }
        
        function getResource() {
            return $this->resource;
        }
        
        function createStream($mode=null) {
            if(isset($this->stream)) {
                return $this->stream;
            }
            
            if(is_null($mode)) {
                $mode = qio\Stream\Mode::ReadWrite;
            }
            return $this->stream = new qio\File\Stream($this->resource,$mode);
        }
        
        function createReader(qio\Stream $stream = null) {
            if(isset($this->reader)) {
                if(!is_null($stream) && $stream !== $this->stream) {
                    $this->reader->setStream($stream);
                }
                
                return $this->reader;
            }
            
            if(is_null($stream)) {
                $stream = $this->createStream(qio\Stream\Mode::Read);
            }
            return $this->reader = new qio\File\Reader($stream);
        }
        
        function createWriter(qio\Stream $stream = null) {
            if(isset($this->writer)) {
                if(!is_null($stream) && $stream !== $this->stream) {
                    $this->writer->setStream($stream);
                }
                
                return $this->writer;
            }
            
            if(is_null($stream)) {
                $stream = $this->createStream(qio\Stream\Mode::WriteOnly);
            }
            
            return $this->writer = new qio\File\Writer($stream);
        }
    }
}