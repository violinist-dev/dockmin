<?php

namespace App\Controller;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DebugController.
 *
 * @package App\Controller
 *
 * @Route("/debug")
 */
class DebugController extends Controller
{
    /**
     * @Route("/hello")
     * @Template()
     */
    public function hello()
    {
        $ssh = new SSH2('10.0.0.11', 22, 1);
        $key = new RSA();
        $key->loadKey(file_get_contents('/home/ubuntu/.ssh/id_rsa'));
        $ssh->login('ubuntu', $key);
        $result = $ssh->exec('docker images');
        return ['result' => $result];
    }

}