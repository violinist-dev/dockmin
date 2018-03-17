<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class Authentication.
 *
 * @package App\EventListener
 */
class Authentication
{

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * Authentication constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Fires upon user login.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $password = $event->getRequest()->request->get('_password');
        $this->doctrine->getRepository(User::class)->loadServerCredentials($password, $event->getAuthenticationToken()->getUser());
    }
}