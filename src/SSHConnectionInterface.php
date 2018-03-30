<?php

namespace App;

use App\Entity\Server;

/**
 * Interface SSHConnectionInterface.
 *
 * @package App
 */
interface SSHConnectionInterface
{

    /**
     * Connects to a remote server.
     *
     * @param Server $server
     */
    public function connect(Server $server);

    /**
     * Execute a command.
     *
     * @param $command
     *
     * @return string
     */
    public function execute($command);
}