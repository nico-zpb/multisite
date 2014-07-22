<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 21/07/2014
 * Time: 21:07
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

namespace Sites\Admin\Zoo\GodparentBundle\Controller;


use Sites\Admin\Zoo\GodparentBundle\Entity\Gift;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GiftController extends Controller
{
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository("AdminZooGodparentBundle:Gift");
        $gifts = $repo->getBySortableGroupsQuery(["category"=>"gift"])->getResult();
        return $this->render("AdminZooGodparentBundle:Gift:index.html.twig",["gifts"=>$gifts]);
    }

    public function newAction()
    {
        $gift = new Gift();

        return $this->render("AdminZooGodparentBundle:Gift:new.html.twig",["gift"=>$gift, "form_errors"=>[]]);
    }

    public function createAction(Request $request)
    {

    }
}
