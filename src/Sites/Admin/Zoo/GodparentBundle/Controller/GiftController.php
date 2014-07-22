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


use Sites\Admin\Zoo\GodparentBundle\Entity\Gift;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GiftController extends Controller
{
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository("AdminZooGodparentBundle:Gift");
        $gifts = $repo->getBySortableGroupsQuery(["category"=>"gift"])->getResult();
        return $this->render("AdminZooGodparentBundle:Gift:index.html.twig",["gifts"=>$gifts]);
    }

    public function newAction()
    {
        $gift = new Gift();

        return $this->render("AdminZooGodparentBundle:Gift:new.html.twig",["gift"=>$gift, "form_errors"=>[]]);
    }

    public function createAction(Request $request)
    {
        $form = $request->request->get("new_gift_form");
        $csrfProvider = $this->container->get("form.csrf_provider");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("new_gift", $form["_token"])){
            throw new AccessDeniedException();
        }

        $errors = [];
        $gift = new Gift();

        if(empty($form["name"])){
            $errors[] = "Votre cadeau doit avoir un nom.";
        }

        if(!empty($form["name"])){

            if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?.,; _-]/",'',$form["name"]) != ""){
                $errors[] = "Le nom de votre cadeau contient des caractères interdits.";
            }

        }
        if(!empty($form["position"])){
            if(preg_replace("/[0-9]+/", "", trim($form["position"])) != ""){
                $errors[] = "La position ne doit contenir que des chiffres";
            }
        }

        if(!empty($form["description"])){
            $form["description"] = htmlentities(strip_tags($form["description"]));
            $form["description"] = trim(preg_replace('/\s\s+/',' ',$form["description"]));
        }

        if(!empty($form["notabene"])){
            $form["notabene"] = htmlentities(strip_tags($form["notabene"]));
            $form["notabene"] = trim(preg_replace('/\s\s+/',' ',$form["notabene"]));
        }

        $gift->setName($form["name"]);
        $gift->setDescription($form["description"]);
        $gift->setPosition($form["position"]);
        $gift->setNotabene($form["notabene"]);

        if($errors){
            return $this->render("AdminZooGodparentBundle:Gift:new.html.twig",["gift"=>$gift, "form_errors"=>$errors]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($gift);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Votre nouveau cadeau " . " a bien été enregistré");
        return $this->redirect($this->generateUrl("admin_zoo_godparent_gifts_homepage"));
    }
}
