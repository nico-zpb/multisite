<?php

namespace Sites\Pagodes\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PagodesBaseBundle:Default:index.html.twig');
    }
}
