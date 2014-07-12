<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 12/07/2014
 * Time: 11:34
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

namespace Sites\Admin\Zoo\FototekBundle\Controller;


use Sites\Admin\Zoo\FototekBundle\Entity\ZFCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryController extends Controller
{
    public function indexAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        return $this->render("AdminZooFototekBundle:Category:index.html.twig", ["categories"=>$cats]);
    }

    public function newAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        $cat = new ZFCategory();
        return $this->render("AdminZooFototekBundle:Category:new.html.twig", ["categories"=>$cats, "form_errors"=>[], "entity"=>$cat]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        if(empty($request->request->get("new_category_form")["_token"]) || !$csrfProvider->isCsrfTokenValid("new_category", $request->request->get("new_category_form")["_token"])){
            throw new AccessDeniedException();
        }
        $form = $request->request->get("new_category_form");
        $errors = [];
        $slug = null;
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
        $catExists = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findOneByName($form["name"]);
        if($catExists){
            $errors[] = "Il existe déjà une catégorie du même nom.";
        }
        $em = $this->getDoctrine()->getManager();
        $cat = new ZFCategory();
        $cat->setName($form["name"]);
        if($form["slug"] != ""){
            $cat->setSlug($form["slug"]);
        }

        if($errors){
            $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
            return $this->render("AdminZooFototekBundle:Category:new.html.twig", ["categories"=>$cats,"form_errors"=>$errors, "entity"=>$cat]);
        }

        $em->persist($cat);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "La catégorie " . $cat->getName() . " a bien été enregistrée.");
        return $this->redirect($this->generateUrl("admin_zoo_fototek_category_homepage"));
    }
}
