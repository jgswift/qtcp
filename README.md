qtcp
==== 

Web socket client/server using php and javascript

[![Build Status](https://travis-ci.org/jgswift/qtcp.png?branch=master)](https://travis-ci.org/jgswift/qtcp)

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/qtcp:dev-master
```

## Dependency

* php 5.5+
* [html5 web sockets](http://caniuse.com/#feat=websockets)
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

### Sample Stream
*[tests/Examples/SampleStream](http://github.com/jgswift/qtcp/tree/master/tests/Examples/SampleStream)*

The sample stream serves as a conceptual prototype to demonstrate the most basic functionality

#### Server

**Configure**

First, configure the host and port by editing the [config.js](http://github.com/jgswift/qtcp/tree/master/tests/Examples/SampleStream/config.js)

If you intend to run the client and server together on the same box, a likely configuration may be the following
```js
var SampleStream = {
    host: 'localhost',
    port: 8081
};
```

**Run**

In terminal or via SSH, navigate to the directory qtcp is located in and start the server
```sh
$ cd vendor/jgswift/qtcp/
$ php tests/Examples/SampleStream/Server.php localhost:8081
```

The server will start and you will see

```
Starting server..
Server started.
```

**[Code](http://github.com/jgswift/qtcp/blob/master/tests/Examples/SampleStream/Server.php)**

```php
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
```

#### Client

Open a web browser and navigate to ```http://localhost/your_project_directory/vendor/jgswift/qtcp/tests/Examples/SampleStream```.  
*Note: Modify path if qtcp is in a different directory.*

A sample application will appear and click the button to send your first packet

**[Code](http://github.com/jgswift/qtcp/blob/master/tests/Examples/SampleStream/index.php)**

```js
qtcp.network.client = new qtcp.client(
    "body",
    new qtcp.stream(
        new qtcp.resource(SampleStream.host,SampleStream.port)
    )
);

// attach packet processor for event packet
qtcp.network.client.attach("event",function(data) {
    $("#response").html(data[0]);
});

// connect to server
qtcp.network.client.connect();

// send event packet with some dummy data
$('input').on('click',function() {
    qtcp.network.client.send(new qtcp.network.packet("event"),{var1:"test"});
});
```

### Currency Stream 
*[tests/Examples/CurrencyStream](http://github.com/jgswift/qtcp/tree/master/tests/Examples/CurrencyStream)*

The currency stream example simulates a currency index which concurrently updates all clients with price changes.

#### Server

**Configure**

Like above, configure the host and port by editing the [config.js](http://github.com/jgswift/qtcp/tree/master/tests/Examples/CurrencyStream/config.js)

If you intend to run the client and server together on the same box, a likely configuration may be the following
```js
var CurrencyStream = {
    host: 'localhost',
    port: 8081
};
```

**Run**

In terminal or via SSH, navigate to the directory qtcp is located in and start the server
```sh
$ cd vendor/jgswift/qtcp/
$ php tests/Examples/CurrencyStream/Server.php
```

Alternatively, you may specify the host/port

```sh
$ php tests/Examples/CurrencyStream/Server.php 0.0.0.0:8081
```

The user initiating the server will need write privileges to the ```tests/Examples/CurrencyStream``` folder for this test

The server will start and you will see

```
Starting server..
Server started.
```

#### Client

Open a web browser and navigate to ```http://localhost/your_project_directory/vendor/jgswift/qtcp/tests/Examples/CurrencyStream```.  
*Note: Modify path if qtcp is in a different directory.*

The price streaming application will list a currency index.  Check any boxes on the left to initiate streaming.