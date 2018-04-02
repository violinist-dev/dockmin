<?php

namespace App\Controller;

use App\DockerConnectionInterface;
use App\Form\ServerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Server;

/**
 * Class ServerController.
 *
 * @package App\Controller
 *
 * @Route("/server")
 */
class ServerController extends Controller
{

    /**
     * @Route(name="server_index")
     * @Route("/index")
     * @Template()
     */
    public function index()
    {
        $servers = $this->getDoctrine()->getRepository(Server::class)->findBy(['owner' => $this->getUser()->getId()]);

        return ['servers' => $servers];
    }

    /**
     * @Route("/add", name="server_add")
     * @Template()
     */
    public function add(Request $request, DockerConnectionInterface $dc)
    {
        $server = new Server();
        $form = $this->createForm(ServerType::class, $server);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Server $server */
            $server = $form->getData();
            $server->setOwner($this->getUser());

            $dc->connect($server);
            $docker_info = $dc->info();

            $server->setDockerInfo($docker_info);
            $server->setOs($docker_info['os']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($server);
            $em->flush();
            return $this->redirectToRoute('server_index');
        }

        return ['form' => $form->createView()];

    }

    /**
     * @Route("/edit/{server}", name="server_edit")
     * @Template()
     */
    public function edit(Request $request, Server $server, DockerConnectionInterface $dc)
    {
        $form = $this->createForm(ServerType::class, $server);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Server $server */
            $server = $form->getData();$dc->connect($server);
                $docker_info = $dc->info();

            $server->setDockerInfo($docker_info);
            $server->setOs($docker_info['os']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($server);
            $em->flush();

            return $this->redirectToRoute('server_index');
        }

        return ['form' => $form->createView(), 'server' => $server];
    }

    /**
     * @Route("/delete/{server}", name="server_delete")
     * @Template()
     */
    public function delete(Request $request, Server $server)
    {
        $form = $this->createFormBuilder()
            ->add('confirmation', SubmitType::class, ['label' => 'Yes, delete server'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($server);
            $em->flush();

            return $this->redirectToRoute('server_index');
        }

        return ['form' => $form->createView(), 'server' => $server];
    }

    /**
     * @Route("/view/{server}", name="server_view")
     * @Template()
     */
    public function view(Request $request, Server $server, DockerConnectionInterface $docker_connection)
    {
        $docker_connection->connect($server);
        return [
            'server' => $server,
            'info' => $docker_connection->info(true),
            'images' => $docker_connection->images(),
            'containers' => $docker_connection->ps(),
        ];
    }
}