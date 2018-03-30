<?php

namespace App\Tests;

use App\Entity\ServerCredential;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class WebTestCase extends KernelTestCase
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var User
     */
    protected $loggedInUser;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * Creates a Client.
     *
     * @param array $options
     *   An array of options to pass to the createKernel class
     * @param array $server
     *   An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        $kernel = static::bootKernel($options);

        $client = $kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Performs a login for a given user.
     */
    protected function logIn($username = 'test_user', $password = 's3cret', array $roles = ['ROLE_AUTHENTICATED'])
    {
        // Login without having connection to the DB and therefor an actual user.
        if (empty($this->entityManager)) {
            $session = $this->client->getContainer()->get('session');

            $firewall = 'main';

            $token = new UsernamePasswordToken($username, null, $firewall, $roles);
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
        }
        // Login with an actual user from the DB, if one is available.
        else {

            $user_repo = $this->entityManager
                ->getRepository(User::class);
            $user = $user_repo->findOneBy(['username' => $username]);
            // If a user by that name already exists, use that.
            // Otherwise, create it.
            if (!$user) {
                $user = new User();
                $user->setUsername($username);
                $user->setPlainPassword($password);
                $this->saveUser($user);
            }

            $crawler = $this->client->request('GET', '/login');
            $form = $crawler->selectButton('login-submit')->form();
            $this->client->submit($form, ['_username' => $username, '_password' => $password]);

            // Reload the user from the DB and set it as currently logged in.
            $this->loggedInUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'test_user']);
            $this->loggedInUser->setPlainPassword($password);
        }
    }

    protected function saveServerCredential(ServerCredential $server_credential)
    {
        $server_credential = parent::saveServerCredential($server_credential);

        // If we have a logged in user available, re-encrypt the credentials with that test users actual password.
        if (!empty($this->loggedInUser)) {
            $user_repo = $this->entityManager
                ->getRepository(User::class);

            $credential = [
                'username' => 'user',
                'password' => 'password',
                'key' => null,
            ];
            $user_repo->encryptServerCredential($this->loggedInUser->getPlainPassword(), $this->loggedInUser, $credential, $server_credential);
        }

        return $this->saveEntity($server_credential);
    }

}