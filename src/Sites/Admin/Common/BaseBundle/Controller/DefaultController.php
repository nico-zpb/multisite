<?php

namespace Sites\Admin\Common\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminCommonBaseBundle:Default:index.html.twig');
    }
}
