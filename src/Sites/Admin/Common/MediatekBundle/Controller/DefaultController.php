<?php

namespace Sites\Admin\Common\MediatekBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $images = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Document")->findAllAlphaOrdered("image");
        return $this->render('AdminCommonMediatekBundle:Default:index.html.twig', ["images"=>$images,"thumbDir"=>$this->container->getParameter("mediatek_images_thumbnails_dirname")]);
    }
}
