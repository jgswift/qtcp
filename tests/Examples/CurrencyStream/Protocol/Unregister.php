<?php
namespace qtcp\Tests\Examples\CurrencyStream\Protocol {
    class Unregister extends \qtcp\Network\Packet {
        function __construct($data = null) {
            parent::__construct($data);
            
            $this->attach('receive',function($client,$e) {
                $app = $client->getApplication();
                $timers = $client->getTimers();
                
                $id = $e['data'][0];
                
                $wrappers = $app->getWrappers();
                
                if(isset($timers[$id])) {
                    $timer = $timers[$id];
                    
                    $name = null;
                    if($wrappers->exists($id)) {
                        $name = $wrappers[$id]->getName();
                    }
                    
                    $app->removeTimer($timer);
                    $client->getTimers()->remove($id);
                    $client->send(new self,[$id,$name]);
                }
            });
        }
    }
}