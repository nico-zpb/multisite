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


use Doctrine\ORM\EntityNotFoundException;
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

    public function deleteAction($id, Request $request)
    {
        $token = $request->query->get("_token");
        $csrfProvider = $this->get("form.csrf_provider");

        if(!$token || !$csrfProvider->isCsrfTokenValid("delete_menu", $token)){
            throw new AccessDeniedException();
        }

        $menu = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu")->find($id);

        if(!$menu){
            throw new EntityNotFoundException();
        }
        $name = $menu->getTitle();
        $em = $this->getDoctrine()->getManager();
        $em->remove($menu);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "Le menu " . $name . " a bien été supprimé.");
        return $this->redirect($this->generateUrl("admin_common_menu_manager_homepage"));
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

    public function editAction($id)
    {
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $menu = $repo->find($id);

        if(!$menu){
            throw new EntityNotFoundException();
        }

        $child = new Menu();
        $cont = $this;
        $html = $repo->childrenHierarchy(
            $menu,
            false,
            [
                "decorate"=>true,
                "nodeDecorator"=>function($node) use (&$cont){
                    return "<span>".$node["title"]." <a href='" .$cont->generateUrl("admin_common_menu_manager_edit_child",["id"=>$node["id"]]). "'>editer</a>| <a href='".$node["link"]."' target='_blank' >visiter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_up_child",["id"=>$node["id"]])."'>monter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_down_child",["id"=>$node["id"]])."'>descendre</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_delete_child",["id"=>$node["id"]])."'>supprimer</a> </span>";
                }
            ]
        );
        return $this->render("AdminCommonMenuManagerBundle:Menu:edit.html.twig", ["update_form_errors"=>[], "add_child_form_errors"=>[],"menu"=>$menu, "child"=>$child, "tree"=>$html]);
    }

    public function updateAction($id, Request $request)
    {
        $csrfProvider = $this->get("form.csrf_provider");
        $form = $request->request->get("update_menu_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("update_menu", $form["_token"])){
            throw new AccessDeniedException();
        }

        $update_errors = [];

        if(empty($form['title'])){
            $update_errors[] = "Le champs 'nom' est requis.";
        }

        if(null != $forbiden = preg_replace('/[a-z0-9_-]/','',$form["title"]) != null){
            $update_errors[] = "Le champs 'nom' contenait des caractères interdits" ;
        }
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $menu = $repo->find($id);

        if(!$menu){
            throw new EntityNotFoundException();
        }



        if($update_errors){
            $child = new Menu();
            $cont = $this;
            $html = $repo->childrenHierarchy(
                $menu,
                false,
                [
                    "decorate"=>true,
                    "nodeDecorator"=>function($node) use (&$cont){
                        return "<span>".$node["title"]." <a href='" .$cont->generateUrl("admin_common_menu_manager_edit_child",["id"=>$node["id"]]). "'>editer</a>| <a href='".$node["link"]."' target='_blank' >visiter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_up_child",["id"=>$node["id"]])."'>monter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_down_child",["id"=>$node["id"]])."'>descendre</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_delete_child",["id"=>$node["id"]])."'>supprimer</a> </span>";
                    }
                ]
            );
            if($menu->getLvl()>0){
                $parent = $repo->getPath($menu)[0];
                return $this->render("AdminCommonMenuManagerBundle:Menu:edit_child.html.twig", ["update_form_errors"=>$update_errors, "add_child_form_errors"=>[],"root"=>$parent,"menu"=>$menu, "child"=>$child, "tree"=>$html]);
            } else {
                return $this->render("AdminCommonMenuManagerBundle:Menu:edit.html.twig", ["update_form_errors"=>$update_errors, "add_child_form_errors"=>[],"menu"=>$menu, "child"=>$child, "tree"=>$html]);
            }
        }

        $menu->setTitle($form["title"]);
        if($form["link"]){
            $menu->setLink(filter_var($form["link"], FILTER_SANITIZE_URL));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($menu);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Le menu " . $menu->getTitle() . " a bien été mis à jour.");

        return $this->redirect($this->generateUrl("admin_common_menu_manager_homepage"));
    }




    public function addChildAction($id, Request $request)
    {
        $csrfProvider = $this->get("form.csrf_provider");
        $form = $request->request->get("add_child_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("add_child_menu",$form["_token"])){
            throw new AccessDeniedException();
        }
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");

        $parent = $repo->find($id);

        if(!$parent){
            throw new EntityNotFoundException();
        }

        $add_errors = [];

        if(empty($form["title"])){
            $add_errors[] = "Le champs 'nom' est requis.";
        }

        if(preg_replace('/[a-z0-9_-]/','',$form["title"]) != null){
            $add_errors[] = "Le champs 'nom' contient des caractères interdits.";
        }


        $child = new Menu();

        $child->setTitle($form["title"]);
        if($form["link"]){
            $child->setLink(filter_var($form["link"], FILTER_SANITIZE_URL));
        }
        if($add_errors){
            $cont = $this;
            $html = $repo->childrenHierarchy(
                $parent,
                false,
                [
                    "decorate"=>true,
                    "nodeDecorator"=>function($node) use (&$cont){
                        return "<span>".$node["title"]." <a href='" .$cont->generateUrl("admin_common_menu_manager_edit_child",["id"=>$node["id"]]). "'>editer</a>| <a href='".$node["link"]."' target='_blank' >visiter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_up_child",["id"=>$node["id"]])."'>monter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_down_child",["id"=>$node["id"]])."'>descendre</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_delete_child",["id"=>$node["id"]])."'>supprimer</a> </span>";
                    }
                ]
            );
            return $this->render("AdminCommonMenuManagerBundle:Menu:edit.html.twig", ["update_form_errors"=>[], "add_child_form_errors"=>$add_errors,"menu"=>$parent, "child"=>$child, "tree"=>$html]);
        }

        $em = $this->getDoctrine()->getManager();
        $child->setParent($parent);
        $em->persist($child);
        $em->flush();
        $this->container->get("session")->getFlashBag()->add("success", "L'entrée' " . $child->getTitle() . " a bien été ajouté à " . $parent->getTitle());

        if($parent->getLvl()>0){
            return $this->redirect($this->generateUrl("admin_common_menu_manager_edit_child", ["id"=>$parent->getId()]));
        } else {
            return $this->redirect($this->generateUrl("admin_common_menu_manager_edit", ["id"=>$parent->getId()]));
        }

    }

    public function editChildAction($id)
    {
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $node = $repo->find($id);
        if(!$node){
            throw new EntityNotFoundException();
        }
        $parent = $repo->getPath($node)[0];
        $child = new Menu();
        $cont = $this;
        $html = $repo->childrenHierarchy(
            $node,
            false,
            [
                "decorate"=>true,
                "nodeDecorator"=>function($node) use ($cont){
                    return "<span>".$node["title"]. ": <a href='".$cont->generateUrl("admin_common_menu_manager_edit_child",["id"=>$node["id"]])."'>editer</a> | <a href='".$node["link"]."' target='_blank' >visiter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_up_child",["id"=>$node["id"]])."'>monter</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_down_child",["id"=>$node["id"]])."'>descendre</a> | <a href='".$cont->generateUrl("admin_common_menu_manager_delete_child",["id"=>$node["id"]])."'>supprimer</a> </span>";
            }]
        );

        return $this->render("AdminCommonMenuManagerBundle:Menu:edit_child.html.twig", ["update_form_errors"=>[],"add_child_form_errors"=>[], "root"=>$parent, "menu"=>$node, "tree"=>$html, "child"=>$child]);
    }

    public function deleteChildAction($id)
    {
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $node = $repo->find($id);
        if(!$node){
            throw new EntityNotFoundException();
        }

        $root = $repo->getPath($node)[0];
        $em = $this->getDoctrine()->getManager();
        $em->remove($node);
        $em->flush();
        return $this->redirect($this->generateUrl("admin_common_menu_manager_edit", ["id"=>$root->getId()]));
    }

    public function getUpChildAction($id)
    {
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $node = $repo->find($id);
        if(!$node){
            throw new EntityNotFoundException();
        }
        $root = $repo->getPath($node)[0];
        $repo->moveUp($node, 1);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirect($this->generateUrl("admin_common_menu_manager_edit", ["id"=>$root->getId()]));
    }

    public function getDownChildAction($id)
    {
        $repo = $this->getDoctrine()->getRepository("AdminCommonMenuManagerBundle:Menu");
        $node = $repo->find($id);
        if(!$node){
            throw new EntityNotFoundException();
        }
        $root = $repo->getPath($node)[0];
        $repo->moveDown($node, 1);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->redirect($this->generateUrl("admin_common_menu_manager_edit", ["id"=>$root->getId()]));
    }


}
