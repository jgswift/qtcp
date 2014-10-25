<?php
namespace qtcp\Application {

    use bubblr\Bubble\CallableBubble;
    
    class BubbleTimer extends Timer {
        protected $bubble;

        public function setCallback(callable $callback) {
            parent::setCallback($callback);
            $this->bubble = new CallableBubble($this->callback);
        }

        public function execute() {
            if(!$this->enabled) { return; }

            return $this->bubble;
        }
    }
}