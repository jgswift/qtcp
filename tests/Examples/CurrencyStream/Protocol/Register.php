<?php
namespace qtcp\Tests\Examples\CurrencyStream\Protocol {
    use qtcp;
    use qio;
    
    class Register extends \qtcp\Network\Packet {
        function __construct($data = null) {
            parent::__construct($data);
            
            $this->attach('receive',function($client,$e) {
                $app = $client->getApplication();
                $timers = $client->getTimers();
                
                $wrappers = $app->getWrappers();
                
                foreach($wrappers as $w) {
                    $id = $w->getID();
                    $name = $w->getName();
                    if($id == $e['data'][0]) {
                        $stream = $w->createStream(qio\Stream\Mode::Read);
                        $reader = $w->createReader($stream);
                        $stream->open();
                        $stream->lock();
                        $value = $reader->readAll();
                        $stream->close();
                            
                        $timer = new qtcp\Application\Timer(0.1,function()use($reader,$stream,$client,$id,$name) {
                            $stream->open();
                            $stream->lock();
                            $stream->seek(0);
                            $value = $reader->readAll();
                            $stream->unlock();
                            $client->send(new Tell(), [
                                $id,
                                $name,
                                $value
                            ]);
                            $stream->close();
                        });
                        
                        $timers->insert($id,$timer);
                        $app->clock->addTimer($timer);
                        
                        $timer->start();
                        
                        $client->send(new self,[$id,$w->getName()]);
                        $client->send(new Tell([
                            $id,
                            $name,
                            $value
                        ]));
                    }
                }
            });
        }
    }
}