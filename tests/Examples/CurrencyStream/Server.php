<?php
$loader = require __DIR__ . '/../../../tests/bootstrap.php';

$host = '0.0.0.0';
$port = 8081;
if(isset($argv[0])) {
    if(strpos($argv[0],':') !== false) {
        list($host, $port) = explode(':',$argv[0]);
    } else {
        $host = $argv[0];
    }
}

$resource = new qtcp\Network\Resource($host,(int)$port);
$engine = new qtcp\Tests\Examples\CurrencyStream\Application($resource);
$engine->run();

