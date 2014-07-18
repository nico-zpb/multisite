<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 18/07/14
 * Time: 08:50
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

namespace Sites\Admin\Zoo\BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ZBTagController extends Controller
{
    public function indexAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->findAllAlphaOrdered();
        return $this->render("AdminZooBlogBundle:ZBTag:index.html.twig",["tags"=>$tags]);
    }

    public function newAction()
    {
        return $this->render("AdminZooBlogBundle:ZBTag:new.html.twig");
    }
} 
