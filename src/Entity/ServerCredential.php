<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerCredentialRepository")
 * @ORM\Table(name="server_credentials")
 * @ORM\HasLifecycleCallbacks()
 */
class ServerCredential
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="user")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Server", mappedBy="serverCredential")
     */
    private $servers;

    /**
     * @ORM\Column(type="text")
     */
    private $credentials;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $encryptionSalt;

    /**
     * @ORM\Column(type="integer")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated;

    /**
     * ServerCredential constructor.
     */
    public function __construct()
    {
        $this->encryptionSalt = substr(md5(random_bytes(255)), 0, 16);
        $this->created = gmdate('U');
        $this->updated = gmdate('U');
        $this->active = true;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->updated = gmdate('U');
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
     * @return Server[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @param Server[] $servers
     */
    public function setServers($servers): void
    {
        $this->servers = $servers;
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param string $credentials
     */
    public function setCredentials($credentials): void
    {
        $this->credentials = $credentials;
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
     * @return resource
     */
    public function getEncryptionSalt()
    {
        return $this->encryptionSalt;
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

}
