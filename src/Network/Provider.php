<?php
namespace qtcp\Network {
    use qtcp;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;

    class Provider {
        private $io;
        private $application;
        private $server;

        function __construct(qtcp\Application $application) {
            $this->application = $application;
            $loop = \React\EventLoop\Factory::create();
            $loop->addPeriodicTimer($application->clock->getSpeed(),function() {
                $this->application->clock->tick();
            });

            $component = new HttpServer(
                new WsServer(
                    $this->server = new \qtcp\Server($application)
                )
            );

            $socket = new \React\Socket\Server($loop);
            
            $resource = $application->getResource();
            $socket->listen($resource->getPort(), $resource->getPath());
            $this->io = new IoServer($component,$socket,$loop);
        }

        public function getIO() {
            return $this->io;
        }

        public function getApplication() {
            return $this->application;
        }

        public function getServer() {
            return $this->server;
        }
    }
}


