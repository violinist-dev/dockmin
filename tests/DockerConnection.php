<?php

namespace App\Tests;

use App\DockerConnectionInterface;
use App\Entity\Server;

class DockerConnection implements DockerConnectionInterface
{

    /**
     * {@inheritdoc}
     */
    public function info($raw = false)
    {
        if ($raw) {
            return 'raw strinng';
        }
        return [
            'containers' => 4,
            'containers_running' => 2,
            'containers_paused' => 1,
            'containers_stopped' => 1,
            'images' => 2,
            'version' => '10.0',
            'storage_driver' => 'overlay2',
            'kernel_version' => '4.4.0',
            'os' => 'Ubuntu',
            'arch' => 'x64_86',
            'cpus' => 4,
            'memory' => 4096,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function images()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function ps()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Server $server)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command)
    {
    }
}