<?php

namespace Sites\Groupes\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GroupesBaseBundle:Default:index.html.twig');
    }
}
