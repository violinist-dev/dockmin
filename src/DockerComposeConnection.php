<?php

namespace App;

/**
 * Class DockerComposeConnection.
 *
 * @package App
 */
class DockerComposeConnection extends SSHConnection
{

    protected $project;

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project): void
    {
        $this->project = $project;
    }


    public function up()
    {

    }

    public function down()
    {

    }
}
