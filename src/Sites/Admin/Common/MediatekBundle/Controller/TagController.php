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


use Sites\Admin\Common\MediatekBundle\Entity\Tag;
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
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");

        if(!$token || !$csrfProvider->isCsrfTokenValid("edit_tag",$token)){
            throw new AccessDeniedException();
        }

        $tag = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($id);
        if(!$tag){
            throw $this->createNotFoundException();
        }

        return $this->render("AdminCommonMediatekBundle:Tag:edit.html.twig", ["entity"=>$tag, "form_errors"=>[]]);
    }

    public function newAction()
    {
        $tag = new Tag();
        return $this->render("AdminCommonMediatekBundle:Tag:new.html.twig", ["form_errors"=>[],"entity"=>$tag]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_tag_form");

        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_tag", $form["_token"])){
            throw new AccessDeniedException();
        }

        $tag = new Tag();

        $errors = [];
        if(array_key_exists("name", $form)){
            $form["name"] = trim(preg_replace('/\s\s+/'," ",$form["name"]));
        }

        if(empty($form["name"])){
            $errors[] = "Le champs 'nom' est manquant.";
        }

        if(!empty($form["name"])){

            if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?, _-]/",'',$form["name"]) != ""){
                $errors[] = "Le champs 'nom' contient des caractères interdits.";
            }
        }
        if(!empty($form["slug"])){
            if(preg_replace("/[a-z0-9-]/",'',$form["slug"]) != ""){
                $errors[] = "Le champs 'alias' contient des caractères interdits.";
            }
        }

        $tagExists = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findOneByName($form["name"]);
        if($tagExists){
            $errors[] = "Il existe déjà un mot-clé du même nom.";
        }

        $tag->setName($form["name"]);
        if(!empty($form["slug"])){
            $tag->setSlug($form["slug"]);
        }

        if($errors){
            return $this->render("AdminCommonMediatekBundle:Tag:new.html.twig", ["form_errors"=>$errors,"entity"=>$tag]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($tag);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "Le mot-clé " . $tag->getName() . " a bien été enregistré.");
        return $this->redirect($this->generateUrl("admin_common_mediatek_tag_homepage"));


    }

    public function updateAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("update_tag_form");

        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("update_tag", $form["_token"])){
            throw new AccessDeniedException();
        }

        $tag = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($id);

        if(!$tag){
            throw $this->createNotFoundException();
        }

        if(array_key_exists("name", $form)){
            $form["name"] = trim(preg_replace('/\s\s+/'," ",$form["name"]));
        }
        $errors = [];

        if(!empty($form["name"]) && $form["name"] != $tag->getName()){
            $tag->setName($form["name"]);
            if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?, _-]/",'',$form["name"]) != ""){
                $errors[] = "Le champs 'nom' contient des caractères interdits.";
            }
            $tagExists = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findOneByName($form["name"]);
            if($tagExists){
                $errors[] = "Un mot-clé porte déjà le même nom.";
            }
        }

        if(!empty($form["slug"]) && $form["slug"] != $tag->getSlug()){
            $tag->setSlug($form["slug"]);
            if(preg_replace("/[a-z0-9-]/",'',$form["slug"]) != ""){
                $errors[] = "Le champs 'alias' contient des caractères interdits.";
            }
        }

        if($errors){
            return $this->render("AdminCommonMediatekBundle:Tag:edit.html.twig", ["form_errors"=>$errors, "entity"=>$tag]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($tag);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success","Le mot-clé " . $tag->getName() . " a bien été mis à jour.");
        return $this->redirect($this->generateUrl("admin_common_mediatek_tag_homepage"));
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
        return $this->redirect($this->generateUrl("admin_common_mediatek_tag_homepage"));
    }
} 
