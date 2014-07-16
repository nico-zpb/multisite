<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrère
 * Date: 16/07/14
 * Time: 11:59
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


use Sites\Admin\Zoo\BlogBundle\Entity\ZBPost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class ZBPostController extends Controller
{
    public function indexAction()
    {}

    public function newAction()
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        $post = new ZBPost();
        return $this->render("AdminZooBlogBundle:ZBPost:new.html.twig", ["form_errors"=>[], "categories"=>$categories, "post"=>$post]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_post_form");
        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_post",$form["_token"])){
            throw new AccessDeniedException();
        }

        $errors = [];

        if(empty($form["category"]) || false === preg_match('/^[0-9]+$/', $form["category"])){
            $errors[] = "Vous devez associer une catégorie à votre article";
        }
        if(!empty($form["category"]) ){
            $cat = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->find($form["category"]);
            if(!$cat){
                $errors[] = "La catégorie associée n'existe pas";
            }
        }
        if(empty($form["title"])){
            $errors[] = "Votre article doit avoir un titre.";
        }
        if(!empty($form["title"]) && preg_replace("/[a-zA-Z0-9éèêàçùëïôâ'!?.,; _-]/",'',$form["title"]) != ""){
            $errors[] = "Le titre de votre article contient des caractères interdits.";
        }
        if(!empty($form["slug"]) && preg_replace("/[a-z0-9-]/",'',$form["slug"]) != ""){
            $errors[] = "Le champs 'alias' contient des caractères interdits.";
        }



        if($errors){
            $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
            return $this->render("AdminZooBlogBundle:ZBPost:new.html.twig", ["form_errors"=>$errors, "categories"=>$categories]);
        }

        var_dump($form);
        die();
    }


}
