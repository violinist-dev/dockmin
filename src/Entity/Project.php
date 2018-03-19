<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="projects")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Server")
     */
    private $server;

    /**
     * @ORM\Column(type="string")
     */
    private $projectPath;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->created = gmdate('U');
        $this->updated = gmdate('U');
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->updated = gmdate('U');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param Server $server
     */
    public function setServer(Server $server): void
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * @param string $projectPath
     */
    public function setProjectPath($projectPath): void
    {
        $this->projectPath = $projectPath;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

}
