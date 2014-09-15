<?php
namespace qtcp\Tests\Examples\CurrencyStream {
    use qtcp;
    use qio;
    
    class Application extends qtcp\Stream\Application {
        function initialize() {
            $this->attach('connect',function($client) {
                
                $wrappers = [];
                
                foreach($this->wrappers as $w) {
                    $wrappers[] = [
                        'id' => $w->getID(),
                        'name' => $w->getName()
                    ];
                }
                
                // informs client of available streams
                $client->send(new Protocol\ListStreams(),['streams'=>$wrappers]);
            });
            
            $streams = [
                'amu',
                'aud',
                'chf',
                'eur',
                'gbp',
                'jpy',
                'usd'
            ];
            
            foreach($streams as $name) {
                $file = new qio\File(__DIR__.'/stream_'.$name);
                $w = new qtcp\Stream\Wrapper($name, $file);
                $this->wrappers->insert($w->getID(), $w);
            }
            
            /*
             * This timer will open up every source file
             * and randomly increase/decrease value plus or minus 20
             */
            $this->clock->addTimer(new qtcp\Application\Timer(0.01, function()use($file) {
                foreach($this->wrappers as $wrapper) {
                    $stream = $wrapper->createStream(\qio\Stream\Mode::ReadWrite);

                    
                    $reader = $wrapper->createReader($stream);

                    if(!$stream->isOpen()) {
                        $stream->open();
                    }
                    $stream->lock();
                    $oldValue = (int)$reader->readAll();
                    
                    $writer = $wrapper->createWriter($stream);

                    $newValue = $oldValue + round(rand(-20,20));
                    if($newValue < 0) {
                        $newValue = 0;
                    }
                    
                    if($newValue > 1000) {
                        $newValue = 1000;
                    }
                    ftruncate ( $stream->getPointer() , 0 );
                    $stream->rewind();
                    $writer->write($newValue);
                    $stream->unlock();
                    $stream->close();
                }
            }));
        }

        public function getProtocol() {
            return new Protocol();
        }
    }
}

