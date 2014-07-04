<?php
$loader = require __DIR__ . '/../../tests/bootstrap.php';

$resource = new qtcp\Network\Resource('0.0.0.0',8081);
$engine = new qtcp\Tests\Examples\Stream\Application($resource);
$engine->run();

