<?php

namespace Sites\Scolaires\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ScolairesBaseBundle:Default:index.html.twig');
    }
}
