<?php

namespace medeirosinacio\Sdk;

class Docker
{
	protected DockerClient $dockerApi;

	public function __construct()
	{
		$this->dockerApi = new DockerClient(host: 'http://docker-api:2375');
	}

	public static function run(string $image, array $command): string
	{
		return (new self())->runCommand($image, $command);
	}

	public function runCommand(string $image, array $command): string
	{
		$this->dockerApi->pullImage($image);

		$containerId = $this->dockerApi->createContainer($image)['Id'];

		$this->dockerApi->startContainer($containerId);

		$execId = $this->dockerApi->execContainer($containerId, $command)['Id'];

		$output = $this->dockerApi->execStartContainer($execId);

		//	$this->dockerApi->removeContainer($containerId);

		return str($output)->squish()->toString();
	}
}
