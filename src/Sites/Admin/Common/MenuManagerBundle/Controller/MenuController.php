<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 13/07/2014
 * Time: 11:03
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

namespace Sites\Admin\Common\MenuManagerBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{
    public function indexAction()
    {
        $roots = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu")->findByLvl(0);

        return $this->render("AdminCommonMenuManagerBundle:Menu:index.html.twig", ["menus"=>$roots]);
    }

    public function newAction()
    {
        return $this->render("AdminCommonMenuManagerBundle:Menu:new.html.twig");
    }

    public function createAction(Request $request)
    {

    }
}
