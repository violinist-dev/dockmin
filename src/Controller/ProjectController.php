<?php

namespace App\Controller;


use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProjectController.
 *
 * @package App\Controller
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route(name="project_index")
     * @Route("/index")
     * @Template()
     */
    public function index()
    {
        $projects = $this->getDoctrine()->getRepository(Project::class)->findBy(['owner' => $this->getUser()->getId()]);

        return ['projects' => $projects];
    }

    /**
     * @Route("/add", name="project_add")
     * @Template()
     */
    public function add(Request $request)
    {

    }

    /**
     * @Route("/edit/{project}", name="project_edit")
     * @Template()
     */
    public function edit(Request $request, Project $project)
    {

    }

    /**
     * @Route("/delete/{project}", name="project_delete")
     * @Template()
     */
    public function delete(Request $request, Project $project)
    {
        $form = $this->createFormBuilder()
            ->add('confirmation', SubmitType::class, ['label' => 'Yes, delete project'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();

            return $this->redirectToRoute('project_index');
        }

        return ['form' => $form->createView(), 'server' => $project];
    }

    /**
     * @Route("/view/{project}", name="project_view")
     * @Template()
     */
    public function view(Request $request, Project $project)
    {
        return [
            'project' => $project,
        ];
    }
}