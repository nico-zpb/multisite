<?php

namespace Sites\Admin\Zoo\FototekBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminZooFototekBundle:Default:index.html.twig');
    }
}
