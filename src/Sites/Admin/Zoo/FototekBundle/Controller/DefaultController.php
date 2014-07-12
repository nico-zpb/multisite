<?php

namespace Sites\Admin\Zoo\FototekBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        if(!$cats){
            $this->container->get("session")->getFlashBag()->add("warning", "Il n'y a pas de catégories enregistrées pour la photothèque. Vous devez en créer au moins une pour pouvoir commencer à 'uploader' des images.");
        }
        return $this->render('AdminZooFototekBundle:Default:index.html.twig');
    }
}
