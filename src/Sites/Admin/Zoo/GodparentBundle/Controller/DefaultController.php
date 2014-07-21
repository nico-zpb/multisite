<?php

namespace Sites\Admin\Zoo\GodparentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminZooGodparentBundle:Default:index.html.twig');
    }
}
