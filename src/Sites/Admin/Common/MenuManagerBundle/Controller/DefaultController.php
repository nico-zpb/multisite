<?php

namespace Sites\Admin\Common\MenuManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminCommonMenuManagerBundle:Default:index.html.twig');
    }
}
