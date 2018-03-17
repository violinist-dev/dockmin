<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController.
 *
 * @package App\Controller
 *
 * @Route("/account")
 */
class AccountController extends Controller
{
    /**
     * @Route(name="account_index")
     * @Route("/index")
     * @Template()
     */
    public function index()
    {
        $account = [];

        return ['account' => $account];
    }

    /**
     * @Route("/edit", name="account_edit")
     * @Template()
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $current_password = $form->get('currentPassword')->getData();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Re-load all credentials to make sure that we have all of them.
            $this->getDoctrine()->getRepository(User::class)->loadServerCredentials($current_password, $user);
            $this->getDoctrine()->getRepository(User::class)->encryptServerCredentials($user->getPlainPassword(), $this->getUser());

        }
        return ['form' => $form->createView()];
    }
}