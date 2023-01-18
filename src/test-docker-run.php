<?php

use medeirosinacio\Sdk\DockerApi;

include_once __DIR__.'/../vendor/autoload.php';

$outputCommand = DockerApi::run(
	image: 'python:3.9.16-slim',
	command: ['python', '-c', "from datetime import datetime; print(datetime.now())"]
);

print_r($outputCommand);
// 2023-01-18 00:28:13.648109

print_r(PHP_EOL);