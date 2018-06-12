<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    /**
     * Social controller.
     *
     * @Route("lounge")
     *
     */
class LoungeController extends Controller
{

/**
 * @Route("/", name="lounge_index")
 * @Method("GET")

 */

    public function indexAction()
    {
        return $this->render('lounge/index.html.twig');
    }
}
