<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CommonController.
 *
 * @package App\Controller
 */
class CommonController extends Controller
{

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function frontpage()
    {
        return [];
    }

}