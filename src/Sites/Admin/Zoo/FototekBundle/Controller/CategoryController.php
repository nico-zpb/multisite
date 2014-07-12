<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 12/07/2014
 * Time: 11:34
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

class CategoryController extends Controller
{
    public function indexAction()
    {
        return $this->render("AdminZooFototekBundle:Category:index.html.twig");
    }

    public function newAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        return $this->render("AdminZooFototekBundle:Category:new.html.twig", ["categories"=>$cats]);
    }
}
