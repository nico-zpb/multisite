<?php

namespace Sites\Admin\Zoo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminZooBlogBundle:Default:index.html.twig');
    }
}
