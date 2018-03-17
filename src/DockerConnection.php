<?php

namespace App;

/**
 * Class DockerCommand.
 *
 * @package App
 */
class DockerConnection extends SSHConnection
{
    /**
     * @param boolean $raw
     *   Return the raw result returned from server if true.
     *
     * @return array|string
     */
    public function info($raw = false)
    {
        $response = $this->execute('docker info');

        if ($raw) {
            return $response;
        }

        $response = explode("\n", $response);

        $docker_info = [];
        foreach ($response as $line) {
            $line_split = explode(":", $line);
            if (count($line_split) == 2) {
                $key = $line_split[0];
                $value = $line_split[1];
                $docker_info[trim($key)] = trim($value);
            }
        }

        $result = [
            'containers' => $docker_info['Containers'],
            'containers_running' => $docker_info['Running'],
            'containers_paused' => $docker_info['Paused'],
            'containers_stopped' => $docker_info['Stopped'],
            'images' => $docker_info['Images'],
            'version' => $docker_info['Server Version'],
            'storage_driver' => $docker_info['Storage Driver'],
            'kernel_version' => $docker_info['Kernel Version'],
            'os' => $docker_info['Operating System'],
            'arch' => $docker_info['Architecture'],
            'cpus' => $docker_info['CPUs'],
            'memory' => $docker_info['Total Memory'],
        ];

        return $result;
    }

    public function images()
    {
        $response = $this->execute('docker images');
        return $response;
    }

    public function ps()
    {
        $response = $this->execute('docker ps -a');
        return $response;
    }
}