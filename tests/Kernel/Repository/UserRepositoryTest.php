<?php

namespace App\Tests\Kernel\Repository;

use App\Entity\ServerCredential;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    /**
     * @var User
     */
    protected $user;

    protected $password;

    /**
     * @var UserRepository
     */
    protected $user_repo;

    /**
     * @var array
     */
    protected $credential;

    public function setUp()
    {
        parent::setUp();

        $this->user_repo = $this->entityManager
            ->getRepository(User::class);

        $this->credential = [
            'username' => 'user',
            'password' => 'password',
            'key' => null,
        ];

        $this->password = 'pwd123';
        $user = new User();
        $user->setPlainPassword($this->password);
        $this->saveUser($user);
        $this->user = $user;
    }

    /**
     * Verify a single server credential can be set.
     */
    public function testEncryptServerCredential()
    {

        $server_credential = new ServerCredential();
        $server_credential->setOwner($this->user);
        $this->saveServerCredential($server_credential);

        $this->user_repo->encryptServerCredential($this->password, $this->user, $this->credential, $server_credential);

        $this->assertGreaterThan(25, strlen($server_credential->getCredentials()));
    }

    /**
     * Test if we can update them in abundance.
     *
     * Create a bunch of Server Credentials and throw them into an array so that we can compare them after they
     * have been updated.
     */
    public function testEncryptServerCredentials()
    {
        $credentials = $this->generateCredentials();

        $this->user_repo->encryptServerCredentials('NewSecretPassword', $this->user);

        $server_credentials = $this->entityManager->getRepository(ServerCredential::class)->findBy(['owner' => $this->user->getId()]);
        // This only tests if the credentials hash value has changed. Not if it was correct.
        foreach ($server_credentials as $server_credential) {
            $this->assertNotEquals($server_credential->getCredentials(), $credentials[$server_credential->getId()]);
        }
    }

    /**
     * Verify that we can load a users server credentials.
     */
    public function testLoadServerCredentials()
    {
        $kernel = self::$kernel;
        $this->generateCredentials();
        $this->user_repo->loadServerCredentials($this->password, $this->user);
        $this->assertCount(4, $kernel->getContainer()->get('session')->get('credentials'));
    }

    /**
     * Generate a series of server credential entities.
     *
     * @return array
     */
    private function generateCredentials($count = 4){
        $credentials = [];
        for ($i = 0; $i < $count; $i++) {
            $server_credential = new ServerCredential();
            $server_credential->setOwner($this->user);
            $this->saveServerCredential($server_credential);
            $this->user_repo->encryptServerCredential($this->password, $this->user, $this->credential, $server_credential);
            $credentials[$server_credential->getId()] = $server_credential->getCredentials();
        }

        return $credentials;
    }
}