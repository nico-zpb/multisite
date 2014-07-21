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


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GiftController extends Controller
{
    public function indexAction()
    {
        return $this->render("AdminZooGodparentBundle:Gift:index.html.twig");
    }
}
