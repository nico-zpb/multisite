<?php

namespace Sites\Pro\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProBaseBundle:Default:index.html.twig');
    }
}
