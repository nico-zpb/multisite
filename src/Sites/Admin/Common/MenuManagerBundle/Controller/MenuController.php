<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 13/07/2014
 * Time: 11:03
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

namespace Sites\Admin\Common\MenuManagerBundle\Controller;


use Sites\Admin\Common\MenuManagerBundle\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MenuController extends Controller
{
    public function indexAction()
    {
        $roots = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu")->findByLvl(0);

        return $this->render("AdminCommonMenuManagerBundle:Menu:index.html.twig", ["menus"=>$roots]);
    }

    public function newAction()
    {
        $errors = [];
        $menu = new Menu();
        return $this->render("AdminCommonMenuManagerBundle:Menu:new.html.twig", ["form_errors"=>$errors,"menu"=>$menu]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->get("form.csrf_provider");
        $form = $request->request->get("new_menu_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("new_menu", $form["_token"])){
            throw new AccessDeniedException();
        }

        $errors = [];

        if(empty($form["title"])){
            $errors[] = "Le champs 'nom' est requis.";
        }

        if(preg_replace('/[a-z0-9_-]/','',$form["title"]) != null){
            $errors[] = "Le champs 'nom' contient des caractères interdits.";
        }

        $test = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu")->findByTitle($form["title"]);

        if($test){
            $errors[] = "Un menu est déjà enregistré sous ce nom.";
        }

        $menu = new Menu();
        $menu->setTitle($form["title"]);

        if($errors){
            return $this->render("AdminCommonMenuManagerBundle:Menu:new.html.twig", ["form_errors"=>$errors,"menu"=>$menu]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($menu);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Le menu '" . $menu->getTitle(). "' a bien été enregistré.");
        return $this->redirect($this->generateUrl("admin_common_menu_manager_homepage"));
    }
}
