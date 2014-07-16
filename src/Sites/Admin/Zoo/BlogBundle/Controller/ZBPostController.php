<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 16/07/14
 * Time: 11:59
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

class ZBPostController extends Controller
{
    public function indexAction()
    {}

    public function newAction()
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        return $this->render("AdminZooBlogBundle:ZBPost:new.html.twig", ["categories"=>$categories]);
    }
} 
