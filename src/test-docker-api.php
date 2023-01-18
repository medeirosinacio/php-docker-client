<?php

use medeirosinacio\Sdk\DockerApi;

include_once __DIR__.'/../vendor/autoload.php';

$docker = new DockerApi();

print_r(
	$docker->version()
);

print_r(PHP_EOL);