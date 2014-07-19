<?php

namespace Sites\Zoo\TestsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ZooTestsBundle:Default:index.html.twig');
    }
}
