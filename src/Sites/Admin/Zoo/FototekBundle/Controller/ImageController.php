<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 12/07/2014
 * Time: 11:33
 */
 /*
           ____________________
  __      /     ______         \
 {  \ ___/___ /       }         \
  {  /       / #      }          |
   {/ ô ô  ;       __}           |
   /          \__}    /  \       /\
<=(_    __<==/  |    /\___\     |  \
   (_ _(    |   |   |  |   |   /    #
    (_ (_   |   |   |  |   |   |
      (__<  |mm_|mm_|  |mm_|mm_|
*/

namespace Sites\Admin\Zoo\FototekBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ImageController extends Controller
{
    public function indexAction()
    {
        return $this->render("AdminZooFototekBundle:Image:index.html.twig");
    }

    public function newAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        if(!$cats){
            $this->container->get("session")->getFlashBag()->add("warning", "Il n'y a pas de catégories enregistrées pour la photothèque. Vous devez en créer au moins une pour pouvoir commencer à 'uploader' des images.");
        }
        return $this->render("AdminZooFototekBundle:Image:new.html.twig",["categories"=>$cats]);
    }
}
