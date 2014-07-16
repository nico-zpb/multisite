<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 16/07/14
 * Time: 09:56
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


use Sites\Admin\Zoo\BlogBundle\Entity\ZBCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ZBCategoryController extends Controller
{
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        return $this->render("AdminZooBlogBundle:ZBCategory:index.html.twig", ["categories"=>$categories]);
    }

    public function newAction()
    {
        $cat = new ZBCategory();
        return $this->render("AdminZooBlogBundle:ZBCategory:new.html.twig", ["form_errors"=>[], "entity"=>$cat]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_category_form");
        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_category",$form["_token"])){
            throw new AccessDeniedException();
        }
        $errors= [];
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
        $form["name"] = trim(preg_replace('/\s\s+/'," ",$form["name"]));
        $catExists = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findOneByName($form["name"]);
        if($catExists){
            $errors[] = "Il existe déjà une catégorie du même nom.";
        }
        $cat = new ZBCategory();
        $cat->setName($form["name"]);
        if(!empty($form["slug"])){
            $cat->setSlug($form["slug"]);
        }
        if($errors){
            return $this->render("AdminZooBlogBundle:ZBCategory:new.html.twig", ["form_errors"=>$errors, "entity"=>$cat]);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($cat);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "La catégorie " . $cat->getName() . " a bien été enregistrée.");
        return $this->redirect($this->generateUrl("admin_zoo_blog_category_homepage"));
    }
} 
