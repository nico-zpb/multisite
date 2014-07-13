<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 12/07/2014
 * Time: 11:33
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


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageController extends Controller
{
    public function indexAction()
    {
        $imgs = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->findAll();
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        return $this->render("AdminZooFototekBundle:Image:index.html.twig",["images"=>$imgs,"categories"=>$cats]);
    }

    public function newAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        if(!$cats){
            $this->container->get("session")->getFlashBag()->add("warning", "Il n'y a pas de catégories enregistrées pour la photothèque. Vous devez en créer au moins une pour pouvoir commencer à 'uploader' des images.");
        }
        return $this->render("AdminZooFototekBundle:Image:new.html.twig",["categories"=>$cats]);
    }

    public function getbycatidAction($id)
    {
        $cat = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->find($id);

        if(!$cat){
            throw new EntityNotFoundException();
        }

        $images = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getAllByCategory($id);

        return $this->render("AdminZooFototekBundle:Image:by_category.html.twig",["category"=>$cat,"images"=>$images]);
    }

    public function filterbycatAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("filter_by_category_form");

        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("filter_by_category",$form["_token"])){
            throw new AccessDeniedException();
        }

        $cat = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->find($form["category"]);

        if(!$cat){
            throw new EntityNotFoundException();
        }

        $images = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getAllByCategory($form["category"]);

        return $this->render("AdminZooFototekBundle:Image:by_category.html.twig",["category"=>$cat,"images"=>$images]);

    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_image_form");

        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_image",$form["_token"])){
            throw new AccessDeniedException();
        }

        $cat = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->find($form["category"]);

        if(!$cat){
            throw new EntityNotFoundException();
        }

        // TODO validation

        // TODO upload

        // TODO get last position

        // TODO save in db

        // TODO crop image

        // TODO flash message

        // TODO redirection

    }

    public function editAction($id)
    {
        // TODO edit ZFImage
    }

    public function updateAction($id, Request $request)
    {
        // TODO update ZFImage
    }

    public function deleteAction($id)
    {
        // TODO delete ZFImage
    }

    public function moveupAction($id)
    {
        // TODO moveup ZFImage
        $image = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $pos = $image->getPosition();
        if($pos>1){
            $newPosition = $pos - 1;
            $this->_move($image, $newPosition);
        }
        //TODO redirect
    }

    public function movedownAction($id)
    {
        // TODO movedown ZFImage
        $image = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $pos = $image->getPosition();
        $newPosition = $pos + 1;
        $this->_move($image, $newPosition);
        //TODO redirect
    }

    public function moveAction($id, Request $request)
    {
        // TODO move ZFImage
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("move_image_form");

        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("move_image",$form["_token"])){
            throw new AccessDeniedException();
        }

        if(false == preg_match('/^[1-9][0-9]$/',$form["position"])){
            throw new AccessDeniedException();
        }

        $image = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }

        $this->_move($image, $form["position"]);

        //TODO redirect
    }

    private function _move($image, $newPosition)
    {

    }
}
