<?php
$loader = require __DIR__ . '/../../../tests/bootstrap.php';

$host = '0.0.0.0';
$port = 8081;
if(isset($argv[1])) {
    if(strpos($argv[1],':') !== false) {
        list($host, $port) = explode(':',$argv[1]);
    } else {
        $host = $argv[1];
    }
}

$resource = new qtcp\Network\Resource($host,(int)$port);
$engine = new qtcp\Tests\Examples\CurrencyStream\Application($resource);
$engine->run();

