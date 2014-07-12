<?php

namespace Sites\Hameaux\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HameauxBaseBundle:Default:index.html.twig');
    }
}
