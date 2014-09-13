qtcp
==== 

An experimental object-oriented ratchet wrapper

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/qtcp:dev-master
```

## Dependency

* php 5.5+
* [cboden/ratchet](http://github.com/cboden/ratchet)
* [symfony/console](http://github.com/symfony/console)
* [jgswift/qtil](http://github.com/jgswift/qtil)
* [jgswift/qio](http://github.com/jgswift/qio)
* [jgswift/observr](http://github.com/jgswift/observr)
* [jgswift/delegatr](http://github.com/jgswift/delegatr)

## Description

qtcp is an experimental abstraction layer for client/server applications using websockets

## Usage

```php
$app = new qtcp\Network\Application(['0.0.0.0',8081]);

$app->attach('connect',function($client) {
    $client->attach('event', function($client, $e) {
        /* do something with event */
    });
    
    $client->attach('disconnect', function() {
        /* teardown client here */
    });
});

$app->run();
```

## Examples

### Currency Stream

**Server**

*Configure*

First, configure the host and port by editing the CurrencyStream [config.js](http://github.com/jgswift/qtcp/tree/master/tests/Examples/CurrencyStream/config.js)

If you intend to run the client and server together on the same box, a likely configuration may be the following
```js
var CurrencyStream = {
    host: 'localhost',
    port: 8081
};
```

*Start*

In terminal or via SSH, navigate to the directory qtcp is located in and start the server
```sh
cd vendor/jgswift/qtcp/
php tests/Examples/CurrencyStream/Server.php
```

Alternatively, you may specify the host/port

```sh
php tests/Examples/CurrencyStream/Server.php 0.0.0.0:8081
```

The user initiating the server will need write privileges to the ```tests/Examples/CurrencyStream``` folder

The server will start and you will see

```
Starting server..
Server started.
```

**Client**

Open a web browser and navigate to ```http://localhost/your_project_directory/vendor/jgswift/qtcp/tests/Examples/CurrencyStream```.  Modify path if qtcp is in a different directory.

A sample application will appear, check any boxes on the left to initiate streaming.

The code for the example server/client application is found in [tests/Examples/CurrencyStream](http://github.com/jgswift/qtcp/tree/master/tests/Examples/CurrencyStream).