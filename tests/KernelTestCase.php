<?php

namespace App\Tests;

use App\Entity\Project;
use App\Entity\Server;
use App\Entity\ServerCredential;
use App\Entity\User;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as KTC;

abstract class KernelTestCase extends KTC
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Run the schema update tool using our entity metadata.
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadatas);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        if (!empty($this->entityManager) && $this->entityManager->isOpen()) {
            $this->entityManager->close();
        }

        // Avoid memory leaks.
        $this->entityManager = null;
    }

    /**
     * Saves an entity for testing purpose.
     *
     * @param $entity
     *
     * @return mixed
     */
    private function saveEntity($entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return $entity;
        } catch (ORMException $ex) {
            print "En error occurred while trying to save entity\n\r" . (string) $ex;
        }

        return null;
    }

    /**
     * @param Project $project
     *
     * @return Project
     */
    protected function saveProject(Project $project)
    {
        $this->saveEntity($project);
        return $project;
    }

    /**
     * @param Server $server
     *
     * @return Server
     */
    protected function saveServer(Server $server)
    {
        $server->setName($server->getName() ?? "" ?: 'Test Server');
        $server->setActive($server->getActive() ?? "" ?: true);

        $this->saveEntity($server);
        return $server;
    }

    /**
     * @param ServerCredential $server_credential
     *
     * @return ServerCredential
     */
    protected function saveServerCredential(ServerCredential $server_credential)
    {

        $server_credential->setName($server_credential->getName() ?? "" ?: 'Test Credentials');
        $server_credential->setActive($server_credential->getActive() ?? "" ?: true);
        $server_credential->setCredentials($server_credential->getCredentials() ?? "" ?: json_encode([]));
        $this->saveEntity($server_credential);

        return $server_credential;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    protected function saveUser(User $user)
    {
        $user->setUsername($user->getUsername() ?? "" ?: 'Test User');
        $user->setEmail($user->getEmail() ?? "" ?: 'test_user@example.com');
        $user->setIsActive($user->getisActive() ?? "" ?: true);
        $user->setPlainPassword($user->getPlainPassword() ?? "" ?: 'password');
        $user->setPassword($user->getPassword() ?? "" ?: 'SuperSecretHashedPassword');

        $this->saveEntity($user);
        return $user;
    }

}