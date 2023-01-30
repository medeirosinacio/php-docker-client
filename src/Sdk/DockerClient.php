<?php

namespace medeirosinacio\Sdk;

use Illuminate\Http\Client\PendingRequest;

class DockerClient
{
    protected PendingRequest $client;

    public function __construct(protected string $host = '')
    {
        $this->client = new PendingRequest();

        $this->client
            ->baseUrl($this->host)
            ->timeout(360)
            ->throw();
    }

    public function version(): array
    {
        return $this->client->get(url: '/version')->json();
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

    public function pullImage(string $image): void
    {
        $this->client->post(url: '/images/create?fromImage='.urlencode($image));
    }

    public function startContainer(string $containerId): void
    {
        $this->client->post(url: '/containers/'.$containerId.'/start')->json();
    }

    public function stopContainer(string $containerId): void
    {
        $this->client->post(url: '/containers/'.$containerId.'/stop');
    }

    public function removeContainer(string $containerId): void
    {
        $this->stopContainer($containerId);

        $this->client->delete(
            url: '/containers/'.$containerId,
            data: [
                'force' => true,
                'v' => true
            ]);
    }

    public function execContainer(string $containerId, array $command): array
    {
        return $this->client->post(
            url: '/containers/'.$containerId.'/exec',
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
            url: '/exec/'.$containerId.'/start',
            data: [
                "Detach" => false,
                "Tty" => true,
            ])->body();
    }
}
