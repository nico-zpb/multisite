<?php

namespace Sites\BeauvalNature\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BeauvalNatureBaseBundle:Default:index.html.twig');
    }
}
