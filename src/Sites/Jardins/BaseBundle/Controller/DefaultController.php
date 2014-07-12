<?php

namespace Sites\Jardins\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JardinsBaseBundle:Default:index.html.twig');
    }
}
