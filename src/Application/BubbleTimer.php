<?php
namespace qtcp\Application {
    use bubblr;
    
    class BubbleTimer extends Timer {
        protected $bubble;
        
        public function setCallback(callable $callback) {
            $this->bubble = new bubblr\Bubble\CallableBubble($callback);
            return $this->callback = $callback;
        }
        
        function execute() {
            if(!$this->enabled) { return; }
            
            $this->getClock()->getBubbler()->getSpout()->push(
                $this->bubble
            );
        }
    }
}