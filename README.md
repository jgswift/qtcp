qtcp
==== 

An experimental object-oriented ratchet wrapper

[![Build Status](https://travis-ci.org/jgswift/qtcp.png?branch=master)](https://travis-ci.org/jgswift/qtcp)

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/qtcp:dev-master
```

## Description

qtcp is an experimental OOP abstraction for client/server applications using ratchet

A simple test application is provided to demonstrate.

In console or via SSH, navigate to the directory qtcp is located in

```
php app/Stream/Server.php
```

The server will start and you will see

```
Starting server..
Server started.
```

Now open a web browser and navigate to ```localhost/qtcp/app/Stream```.  Modify path if qtcp is in a different directory.

A sample application will appear, check any boxes on the left to initiate streaming.

The example server application is found in ````tests/Examples/Stream````.

The example client application is found in ````app/Stream````. 
