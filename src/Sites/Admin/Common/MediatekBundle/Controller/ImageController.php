<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 20/07/2014
 * Time: 15:24
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


use Sites\Admin\Common\MediatekBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageController extends Controller
{
    public function newAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
        $img = new Document();
        $img->setDocType("image");

        return $this->render("AdminCommonMediatekBundle:Image:new.html.twig", ["form_errors"=>[],"image"=>$img,"tags"=>$tags]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_image_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("new_image", $form["_token"])){
            throw new AccessDeniedException();
        }



    }


}
