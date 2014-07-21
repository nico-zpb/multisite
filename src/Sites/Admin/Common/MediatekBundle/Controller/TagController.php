<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 21/07/14
 * Time: 12:51
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

namespace Sites\Admin\Common\MediatekBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TagController extends Controller
{
    public function indexAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
        return $this->render("AdminCommonMediatekBundle:Tag:index.html.twig",["tags"=>$tags]);
    }
} 
