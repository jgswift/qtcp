<?php
$host = '0.0.0.0';
$port = 8081;
foreach($argv as $arg) {
    if(strpos($arg,':') !== false) {
        list($host, $port) = explode(':',$arg);
    } elseif(is_numeric($arg)) {
        $port = $arg;
    } elseif(substr_count($arg,'.') === 4 || substr_count($arg,'.') === 5) {
        $host = $arg;
    }
}