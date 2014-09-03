qtcp
==== 

An experimental object-oriented ratchet wrapper

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/qtcp:dev-master
```

## Description

qtcp is an experimental OOP abstraction for client/server applications using websockets (cboden/ratchet)

## Usage

A simple test application is provided to demonstrate.

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

In terminal or via SSH, navigate to the directory qtcp is located in and run the following command

```sh
cd /var/www/qtcp
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

Open a web browser and navigate to ```localhost/qtcp/tests/Examples/CurrencyStream```.  Modify path if qtcp is in a different directory.

A sample application will appear, check any boxes on the left to initiate streaming.

The code for the example server/client application is found in [tests/Examples/CurrencyStream](http://github.com/jgswift/qtcp/tree/master/tests/Examples/CurrencyStream).