<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerRepository")
 * @ORM\Table(name="servers")
 * @ORM\HasLifecycleCallbacks()
 */
class Server
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $ip;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $port;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="array")
     */
    private $dockerInfo;

    /**
     * @ORM\Column(type="string")
     */
    private $os;

    /**
     * @ORM\ManyToOne(targetEntity="user")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="ServerCredential", inversedBy="servers")
     */
    private $serverCredential;

    public function __construct()
    {
        $this->created = gmdate('U');
        $this->updated = gmdate('U');
        $this->active = true;
        $this->port = 22;
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
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return array
     */
    public function getDockerInfo()
    {
        return $this->dockerInfo;
    }

    /**
     * @param array $dockerInfo
     */
    public function setDockerInfo(array $dockerInfo): void
    {
        $this->dockerInfo = $dockerInfo;
    }

    /**
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param string $os
     */
    public function setOs($os): void
    {
        $this->os = $os;
    }

    /**
     * @return ServerCredential
     */
    public function getServerCredential()
    {
        return $this->serverCredential;
    }

    /**
     * @param ServerCredential $serverCredential
     */
    public function setServerCredential(ServerCredential $serverCredential): void
    {
        $this->serverCredential = $serverCredential;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

}
