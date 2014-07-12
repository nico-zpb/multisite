<?php

namespace Sites\Hotels\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HotelsBaseBundle:Default:index.html.twig');
    }
}
