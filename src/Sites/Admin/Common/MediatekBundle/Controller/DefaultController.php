<?php

namespace Sites\Admin\Common\MediatekBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminCommonMediatekBundle:Default:index.html.twig');
    }
}
