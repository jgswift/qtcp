<?php
namespace qtcp {
    interface Application {
        function getResource();
        function getConsole();
        function getProtocol();
        function run();
    }
}