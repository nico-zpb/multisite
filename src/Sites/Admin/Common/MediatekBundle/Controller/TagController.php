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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TagController extends Controller
{
    public function indexAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
        return $this->render("AdminCommonMediatekBundle:Tag:index.html.twig",["tags"=>$tags]);
    }

    public function editAction($id, Request $request)
    {

    }

    public function newAction()
    {

    }

    public function createAction($id, Request $request)
    {

    }

    public function updateAction($id, Request $request)
    {

    }

    public function deleteAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");
        if(!$token || !$csrfProvider->isCsrfTokenValid("delete_tag", $token)){
            throw new AccessDeniedException();
        }
        $tag = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($id);
        if(!$tag){
            throw $this->createNotFoundException();
        }
        $name = $tag->getName();
        $em = $this->getDoctrine()->getManager();
        $documents = $tag->getDocuments();
        foreach($documents as $doc){
            $doc->removeTag($tag);
            $em->persist($doc);
        }
        $em->remove($tag);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "Le mot-clé " . $name . " a bien été supprimé.");
        return $this->redirect($this->generateUrl("admin_common_tag_homepage"));
    }
} 
