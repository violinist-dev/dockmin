<?php

namespace App;

/**
 * Interface DockerConnectionInterface.
 *
 * @package App
 */
interface DockerConnectionInterface extends SSHConnectionInterface
{

    /**
     * Get general information from the Docker instance.
     *
     * @param boolean $raw
     *   Return the raw result returned from server if true.
     *
     * @return array|string
     */
    public function info($raw = false);

    /**
     * Retrieve a list of images available on the remote Docker instance.
     */
    public function images();

    /**
     * Retrieve a list of active containers running on the Docker instance.
     */
    public function ps();
}