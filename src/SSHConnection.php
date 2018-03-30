<?php

namespace App;

use App\Entity\Server;
use App\Entity\User;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class SSHConnection implements SSHConnectionInterface
{

    /**
     * @var SSH2
     */
    protected $ssh;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $token_storage, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->user = $token_storage->getToken()->getUser();
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Server $server)
    {

        $this->server = $server;

        $all_credentials = $this->session->get('credentials');
        if (!empty($server->getServerCredential()) && isset($all_credentials[$server->getServerCredential()->getId()])) {
            $credentials = $all_credentials[$server->getServerCredential()->getId()];

            $ssh = new SSH2($server->getIp(), $server->getPort(), 1);

            // Load an authentication method. We prioritize key, if it is set.
            // If key is not set we try password.
            $pass_key = null;
            if (!empty($credentials['key'])) {
                $pass_key = new RSA();
                $key_content = $credentials['key'];
                if (is_file($key_content)) {
                    $key_content = file_get_contents($key_content);
                }
                $pass_key->loadKey($key_content);
            } elseif (!empty($credentials['password'])) {
                $pass_key = $credentials['password'];
            }

            $ssh->login($credentials['username'], $pass_key);
            $this->ssh = $ssh;
        }
        else {
            $this->logger->warning('Unable to find usable Server Credentials');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command)
    {
        if (!empty($this->ssh)) {
            $result = $this->ssh->exec($command);
            $this->logger->info('Command executed towards %server% (%ip%): %command%', ['server' => $this->server->getName(), 'ip' => $this->server->getIp(), 'command' => $command]);
            return $result;
        }
        else {
            $this->logger->error('Unable to find a usable SSH connection to remote instance');
            throw new \Exception('Unable to find a usable SSH connection to remote instance');
        }
    }
}