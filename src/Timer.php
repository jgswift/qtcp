<?php
namespace qtcp {
    interface Timer {
        function getInterval();

        function getCallback();
        
        function stop();
        
        function start();
    }
}