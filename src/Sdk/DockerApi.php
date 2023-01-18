<?php

namespace medeirosinacio\Sdk;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;

class DockerApi
{
	protected string $host = 'http://docker-api:2375';

	protected PendingRequest $client;

	public function __construct()
	{
		$this->client = new PendingRequest();

		$this->client
			->baseUrl($this->host)
			->timeout(360)
			->throw();
	}

	public function createContainer(string $image): array
	{
		return $this->client->post(
			url: '/containers/create',
			data: [
				'Image' => $image,
				'Entrypoint' => ["tail", "-f", "/dev/null"]
			])->json();
	}

	public function version(): array
	{
		return $this->client->get(url: '/version')->json();
	}

	public function pullImage(string $image): void
	{
		$this->client->post(url: '/images/create?fromImage='.urlencode($image));
	}

	public function startContainer(string $containerId): void
	{
		$this->client->post(url: sprintf('/containers/%s/start', $containerId))->json();
	}

	public function stopContainer(string $containerId): void
	{
		$this->client->post(url: sprintf('/containers/%s/stop', $containerId));
	}

	public function killContainer(string $containerId): void
	{
		$this->client->post(url: sprintf('/containers/%s/kill', $containerId));
	}

	public function removeContainer(string $containerId): void
	{
		$this->stopContainer($containerId);

		$this->client->delete(
			url: sprintf('/containers/%s', $containerId),
			data: [
				'force' => true,
				'v' => true
			]);
	}

	public function execContainer(string $containerId, array $command): array
	{
		return $this->client->post(
			url: sprintf('/containers/%s/exec', $containerId),
			data: [
				"AttachStdin" => true,
				"AttachStdout" => true,
				"AttachStderr" => true,
				"DetachKeys" => "ctrl-p,ctrl-q",
				"Tty" => true,
				"Privileged" => true,
				"User" => 'root',
				"Cmd" => $command,
			])->json();
	}

	public function execStartContainer(string $containerId): string
	{
		return $this->client->post(
			url: sprintf('/exec/%s/start', $containerId),
			data: [
				"Detach" => false,
				"Tty" => true,
			])->body();
	}

	public static function run(string $image, array $command): string
	{
		$docker = new self();

		$docker->pullImage($image);

		$containerId = $docker->createContainer($image)['Id'];

		$docker->startContainer($containerId);

		$execId = $docker->execContainer($containerId, $command)['Id'];

		$output = $docker->execStartContainer($execId);

		//	$docker->removeContainer($containerId);

		return Str::squish($output);
	}
}
