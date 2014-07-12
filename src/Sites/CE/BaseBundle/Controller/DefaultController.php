<?php

namespace Sites\CE\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CEBaseBundle:Default:index.html.twig');
    }
}
