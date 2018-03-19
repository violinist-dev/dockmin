<?php

namespace App;
use App\Entity\Project;

/**
 * Class DockerComposeConnection.
 *
 * @package App
 */
class DockerComposeConnection extends SSHConnection
{

    /**
     * @var Project|null
     */
    protected $project;

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param project $project
     */
    public function setProject(Project $project): void
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
