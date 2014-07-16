<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 16/07/14
 * Time: 09:56
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

class ZBCategoryController extends Controller
{
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        return $this->render("AdminZooBlogBundle:ZBCategory:index.html.twig", ["categories"=>$categories]);
    }

    public function newAction()
    {
        return $this->render("AdminZooBlogBundle:ZBCategory:new.html.twig");
    }
} 
