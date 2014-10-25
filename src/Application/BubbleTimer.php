<?php
namespace qtcp\Application {
    use bubblr;
    
    class BubbleTimer extends Timer {
        
        
        function execute() {
            if(!$this->enabled) { return; }
            
            //return new bubblr\Bubble\CallableBubble($this->callback);
            
            $this->getClock()->getBubbler()->getSpout()->push(
                new bubblr\Bubble\CallableBubble($this->callback)
            );
        }
    }
}