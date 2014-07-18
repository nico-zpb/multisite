<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 18/07/14
 * Time: 08:50
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


use Sites\Admin\Zoo\BlogBundle\Entity\ZBTag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ZBTagController extends Controller
{
    public function indexAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->findAllAlphaOrdered();
        return $this->render("AdminZooBlogBundle:ZBTag:index.html.twig",["tags"=>$tags]);
    }

    public function newAction()
    {
        $tag = new ZBTag();
        return $this->render("AdminZooBlogBundle:ZBTag:new.html.twig", ["form_errors"=>[], "entity"=>$tag]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_tag_form");
        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_tag",$form["_token"])){
            throw new AccessDeniedException();
        }
        $errors= [];
        if(array_key_exists("name", $form)){
            $form["name"] = trim(preg_replace('/\s\s+/'," ",$form["name"]));
        }

        if(empty($form["name"])){
            $errors[] = "Le champs 'nom' est manquant.";
        }

        if(!empty($form["name"])){

            if(preg_replace("/[a-zA-Z0-9éèêàçùëïôâ'!?, _-]/",'',$form["name"]) != ""){
                $errors[] = "Le champs 'nom' contient des caractères interdits.";
            }
        }
        if(!empty($form["slug"])){
            if(preg_replace("/[a-z0-9-]/",'',$form["slug"]) != ""){
                $errors[] = "Le champs 'alias' contient des caractères interdits.";
            }
        }

        $tagExists = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->findOneByName($form["name"]);
        if($tagExists){
            $errors[] = "Il existe déjà un mot-clé du même nom.";
        }

        $tag = new ZBTag();
        $tag->setName($form["name"]);
        if(!empty($form["slug"])){
            $tag->setSlug($form["slug"]);
        }
        if($errors){
            return $this->render("AdminZooBlogBundle:ZBTag:new.html.twig", ["form_errors"=>$errors, "entity"=>$tag]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($tag);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "Le mot-clé " . $tag->getName() . " a bien été enregistré.");
        return $this->redirect($this->generateUrl("admin_zoo_blog_tag_homepage"));
    }
} 
