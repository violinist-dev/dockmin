<?php

namespace App\Controller;

use App\Entity\ServerCredential;
use App\Entity\User;
use App\Form\ServerCredentialType;
use ParagonIE\Halite\HiddenString;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ParagonIE\Halite\Symmetric\Crypto as SymmetricCrypto;
use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class ServerCredentialsController.
 *
 * @package App\Controller
 *
 * @Route("/server-credential")
 */
class ServerCredentialsController extends Controller
{

    /**
     * @Route("", name="server_credentials_index")
     * @Template()
     */
    public function index()
    {
        $credentials = $this->getDoctrine()->getRepository(ServerCredential::class)->findBy(['owner' => $this->getUser()->getId()]);
        return ['credentials' => $credentials];
    }

    /**
     * @Route("/add", name="server_credentials_add")
     * @Template()
     */
    public function add(Request $request)
    {
        $server_credential = new ServerCredential();

        $form = $this->createForm(ServerCredentialType::class, $server_credential);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ServerCredential $server_credential */
            $server_credential = $form->getData();
            $credentials = [
                'username' => $form->get('username')->getData(),
                'password' => $form->get('password')->getData(),
                'key' => $form->get('key')->getData(),
            ];

            $current_password = $form->get('currentPassword')->getData();

            $server_credential->setOwner($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($server_credential);
            $em->flush();

            $this->getDoctrine()->getRepository(User::class)->encryptServerCredential($current_password, $this->getUser(), $credentials, $server_credential);
            $this->getDoctrine()->getRepository(User::class)->loadServerCredentials($current_password, $this->getUser());

            return $this->redirectToRoute('server_credentials_index');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/delete/{server_credential}", name="server_credentials_delete")
     * @Template()
     */
    public function delete(Request $request, ServerCredential $server_credential)
    {
        $form = $this->createFormBuilder()
            ->add('currentPassword', PasswordType::class, ['label' => 'Current Dockmin password', 'constraints' => new UserPassword()])
            ->add('confirmation', SubmitType::class, ['label' => 'Yes, delete credentials'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $current_password = $form->get('currentPassword')->getData();
            $em = $this->getDoctrine()->getManager();
            $em->remove($server_credential);
            $em->flush();
            $this->getDoctrine()->getRepository(User::class)->loadServerCredentials($current_password, $this->getUser());
            return $this->redirectToRoute('server_credentials_index');
        }
        return ['form' => $form->createView(), 'credentials' => $server_credential];
    }

}