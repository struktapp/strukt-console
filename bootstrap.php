<?php

// error_reporting(E_ALL|~E_STRICT);

$loader = require 'vendor/autoload.php';
$loader->add('Strukt', __DIR__.'/src/');
$loader->add('Command', __DIR__.'/fixtures/src/');