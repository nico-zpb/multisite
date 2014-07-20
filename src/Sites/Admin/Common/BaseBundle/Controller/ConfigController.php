<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 20/07/2014
 * Time: 11:57
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

namespace Sites\Admin\Common\BaseBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigController extends Controller
{
    public function indexAction()
    {
        return $this->render("AdminCommonBaseBundle:Config:index.html.twig");
    }
}
