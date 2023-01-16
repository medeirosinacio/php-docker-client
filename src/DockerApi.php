<?php

namespace medeirosinacio\app;

use App\Interfaces\Containerization;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;
use function App\Services\Sdk\app;

class DockerApi implements Containerization
{
    protected string $host = 'http://docker-api:2375';

    public function __construct(protected PendingRequest $client)
    {
        $this->client
            ->baseUrl($this->host)
            ->throw();
    }

    public function createContainer(string $image)
    {
        return $this->client->post('/containers/create', [
            'Image' => $image,
            'Entrypoint' => ["tail", "-f", "/dev/null"]
        ])['Id'];
    }

    public function version()
    {
        return $this->client->get('/version')->json();
    }

    public function pullImage(string $image)
    {
        return $this->client->post('/images/create?fromImage='.urlencode($image))->json();
    }

    public function container(string $containerId)
    {
        return $this->client->get(sprintf('/containers/%s/json', $containerId))->json();
    }

    public function listContainers()
    {
        return $this->client->get('/containers/json')->json();
    }

    public function startContainer(string $containerId)
    {
        return $this->client->post(sprintf('/containers/%s/start', $containerId))->json();
    }

    public function waitContainer(string $containerId)
    {
        return $this->client->post(sprintf('/containers/%s/wait', $containerId))->json();
    }

    public function statsContainer(string $containerId)
    {
        return $this->client->get(sprintf('/containers/%s/stats', $containerId))->json();
    }

    public function outputContainer(string $containerId)
    {
        return $this->client->get(sprintf('/containers/%s/logs', $containerId), ['stderr' => 1, 'stdout' => 1]);
    }

    public function outputJsonContainer(string $containerId)
    {
        return json_decode(str($this->outputContainer($containerId)->body())->ascii()->toString(), true);
    }

    public function stopContainer(string $containerId)
    {
        return $this->client->post(sprintf('/containers/%s/stop', $containerId));
    }

    public function killContainer(string $containerId)
    {
        return $this->client->post(sprintf('/containers/%s/kill', $containerId));
    }

    public function removeContainer(string $containerId)
    {
        $this->stopContainer($containerId);

        return $this->client->delete(sprintf('/containers/%s', $containerId), [
            'force' => true,
            'v' => true
        ]);
    }

    public function archiveContainer(string $containerId)
    {
        return $this->client->get(sprintf('/containers/%s/archive', $containerId), [
            'path' => '/example.json'
        ]);
    }

    public function execContainer(string $containerId, array $command)
    {
        return $this->client->post(sprintf('/containers/%s/exec', $containerId), [
            "AttachStdin" => true,
            "AttachStdout" => true,
            "AttachStderr" => true,
            "DetachKeys" => "ctrl-p,ctrl-q",
            "Tty" => true,
            "Privileged" => true,
            "User" => 'root',
            "Cmd" => $command,
        ])->json()['Id'];
    }

    public function execStartContainer(string $containerId)
    {
        return $this->client->post(sprintf('/exec/%s/start', $containerId), [
            "Detach" => false,
            "Tty" => true,
        ])->body();
    }

    public function execOutputContainer(string $containerId)
    {
        return $this->client->get(sprintf('/exec/%s/json', $containerId));
    }

    public function attach(string $containerId)
    {
        return $this->client->post(sprintf('/containers/%s/attach', $containerId),
            ['stderr' => 1, 'stdout' => 1, 'stream' => 0, 'logs' => 1]);
    }

    public static function run(string $image, array $command): string
    {
        $docker = app(static::class);

        $docker->pullImage($image);

        $containerId = $docker->createContainer($image);

        $docker->startContainer($containerId);

        $execId = $docker->execContainer($containerId, $command);

        $output = $docker->execStartContainer($execId);

        $docker->removeContainer($containerId);

        return Str::squish($output);
    }
}
