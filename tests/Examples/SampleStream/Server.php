<?php
require __DIR__ . '/../../../tests/bootstrap.php';
require __DIR__ . '/../args.php';

$app = new qtcp\Network\Application([$host,$port]);

$app->attach('connect',function($client) {
    $client->attach('event', function($client, $e) {
        /* send reply */
        $client->send(new qtcp\Network\Packet('event',['hello world!']));
    });
    
    $client->attach('disconnect', function() {
        /* do something with disconnect */
    });
});

$app->run();
