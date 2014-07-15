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
use Sites\Admin\Zoo\FototekBundle\Entity\ZFImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageController extends Controller
{
    public function indexAction()
    {
        $imgs = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->findByIsArchived(false);
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        $thumbnailsDir = $this->container->getParameter("zoo_fototek_web_dir") . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/";
        return $this->render("AdminZooFototekBundle:Image:index.html.twig",["images"=>$imgs,"categories"=>$cats, "thumb_dir"=>$thumbnailsDir]);
    }

    public function newAction()
    {
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        if(!$cats){
            $this->container->get("session")->getFlashBag()->add("warning", "Il n'y a pas de catégories enregistrées pour la photothèque. Vous devez en créer au moins une pour pouvoir commencer à 'uploader' des images.");
        }
        return $this->render("AdminZooFototekBundle:Image:new.html.twig",["form_errors"=>[],"categories"=>$cats]);
    }

    public function getbycatidAction($id)
    {
        $cat = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->find($id);

        if(!$cat){
            throw new EntityNotFoundException();
        }
        $thumbnailsDir = $this->container->getParameter("zoo_fototek_web_dir") . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/";
        $images = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getAllByCategory($id);

        return $this->render("AdminZooFototekBundle:Image:by_category.html.twig",["category"=>$cat,"images"=>$images, "thumb_dir"=>$thumbnailsDir]);
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
        $thumbnailsDir = $this->container->getParameter("zoo_fototek_web_dir") . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/";
        $images = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getAllByCategory($form["category"]);

        return $this->render("AdminZooFototekBundle:Image:by_category.html.twig",["category"=>$cat,"images"=>$images, "thumb_dir"=>$thumbnailsDir]);

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
        $errors = [];
        $suffix = "";
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get("new_image_form")["file"];
        if(!$file instanceof UploadedFile || !$file->isValid()){
            $errors[] = "Pas de fichier à télécharger.";
        }
        $allowedMimes = $this->container->getParameter("zoo_fototek_allowed_mime_types");
        if(!in_array($file->getMimeType(), $allowedMimes)){
            $errors[] = "Le fichier n'est pas du bon type";
        }
        if(!empty($form["name"]) && preg_replace('/[a-zA-Z0-9_-]/','',$form["name"]) !== ""){
            $errors[] = "Le champ 'nom' contient des caratères interdits.";
        }
        if(!empty($form["slug"]) && preg_replace('/[a-z0-9-]/','',$form["slug"]) !== ""){
            $errors[] = "Le champ 'alias' contient des caratères interdits.";
        }
        if($errors){
            $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
            return $this->render("AdminZooFototekBundle:Image:new.html.twig",["categories"=>$cats,"form_errors"=>$errors]);
        }
        if(!empty($form["title"])){
            $form["title"] = trim(htmlentities(strip_tags(preg_replace('/\s\s+/',' ',$form["title"])), ENT_QUOTES|ENT_HTML5,"UTF-8"));
        }
        $form["title"] .= " &copy; ZooParc de Beauval";
        if(!empty($form["date"])){
            $date = new \DateTime("now", new \DateTimeZone("Europe/Paris"));
            $suffix .= "_" . $date->format("d-m-y") ;
        }
        $size = getimagesize($file->getRealPath());
        if(!empty($form["dims"])){
            $suffix .= "_" . $size[0] . "x" . $size[1];
        }
        if(!empty($form["name"])){
            $name = $form["name"] . $suffix . "." . $file->guessExtension();
        } else {
            $tmp = $file->getClientOriginalName();
            if(false !== $pos = strrpos($tmp, ".")){
                $tmp = substr($tmp, 0, $pos);
            }
            $name = $tmp . $suffix . "." . $file->guessExtension();
        }
        $image = new ZFImage();
        $image->setName($name);
        if(!empty($form["slug"])){
            $image->setSlug($form["slug"]);
        }
        $image->setTitle(trim($form["title"]));
        $image->setHeight($size[1]);
        $image->setWidth($size[0]);
        $image->setExtension($file->guessExtension());
        $image->setCountMr(0);
        $image->setCountOriginal(0);
        $image->setAbsolutePath($this->container->getParameter("zoo_fototek_base_dir"));
        $image->setWebPath($this->container->getParameter("zoo_fototek_web_dir"));
        $image->setCategory($cat);
        $pos = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getMaxPosition($cat->getId());
        $pos = ($pos === null) ? 1 : $pos + 1;
        $image->setPosition($pos);
        $file->move($this->container->getParameter("zoo_fototek_base_dir") . "/" . $this->container->getParameter("zoo_fototek_originals_dirname"), $name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();
        $this->copyAndRedim($image, $this->container->getParameter("zoo_fototek_mds_dirname"), $this->container->getParameter("zoo_fototek_mr_max_size"), 100);
        $this->copyAndRedim($image, $this->container->getParameter("zoo_fototek_slides_dirname"), $this->container->getParameter("zoo_fototek_slide_max_size"));
        $this->makeThumbnail($image, $this->container->getParameter("zoo_fototek_thumbnails_dirname"), $this->container->getParameter("zoo_fototek_thumbnail_width"), $this->container->getParameter("zoo_fototek_thumbnail_height"));



        $this->container->get("session")->getFlashBag()->add("success","L'image '" . $image->getName() . " a bien été enregistrée.");
        return $this->redirect($this->generateUrl("admin_zoo_fototek_image_homepage"));
    }


    private function copyAndRedim(ZFImage $image, $dirname = "originales", $maxSize = 1000, $quality = 75)
    {
        $width = $image->getWidth();
        $height = $image->getHeight();
        $newWidth = 0;
        $newHeight = 0;
        if($width >= $height ){
            $ratio = 1 / ($width / $height);
            $newWidth = $maxSize;
            $newHeight = $maxSize * $ratio;
        }
        if($width < $height){
            $ratio = 1 / ($height / $width);
            $newHeight = $maxSize;
            $newWidth = $maxSize * $ratio;
        }
        $gdImage = imagecreatetruecolor($newWidth, $newHeight);
        $copyImage = imagecreatefromjpeg($image->getAbsolutePath() . "/" . $this->container->getParameter("zoo_fototek_originals_dirname") . "/" . $image->getName());
        imagecopyresampled($gdImage, $copyImage, 0,0,0,0, $newWidth, $newHeight, $width, $height);
        imagejpeg($gdImage, $image->getAbsolutePath() . "/" . $dirname . "/" . $image->getName(), $quality);
        imagedestroy($gdImage);
    }

    private function makeThumbnail(ZFImage $image, $dirname = "originales", $width = 300, $height = 450, $quality = 75)
    {


        $iniWidth = $image->getWidth();
        $iniHeight = $image->getHeight();

        $gdImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($gdImage, 255,255,255);
        imagefill($gdImage,0,0, $white);
        $copyImage = imagecreatefromjpeg($image->getAbsolutePath() . "/" . $this->container->getParameter("zoo_fototek_originals_dirname") . "/" . $image->getName());
        $thumbIsLandscape = ( $width >= $height ) ? true : false;

        if($iniWidth>=$iniHeight){
            // paysage
            if($thumbIsLandscape){
                $newHeight = $width * ($iniHeight/$iniWidth);
                if($newHeight>=$height){
                    //ok
                    imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
                } else {
                    $newWidth = $height * ($iniWidth/$iniHeight);
                    imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
                }
            } else {
                $newWidth = $height * ($iniWidth/$iniHeight);
                imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
            }

        } else {
            //portrait
            if($thumbIsLandscape){
                $newHeight = $width * ($iniHeight/$iniWidth);
                imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
            } else {
                $newWidth = $height * ($iniWidth/$iniHeight);
                if($newWidth>=$width){
                    //ok
                    imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
                } else {
                    $newHeight = $width * ($iniHeight/$iniWidth);
                    imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
                }
            }
        }
        imagejpeg($gdImage, $image->getAbsolutePath() . "/" . $dirname . "/" . $image->getName(), $quality);
        imagedestroy($gdImage);
    }

    public function editAction($id)
    {
        // TODO edit ZFImage
    }

    public function updateAction($id, Request $request)
    {
        // TODO update ZFImage
    }

    public function deleteAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");
        if(!$token || !$csrfProvider->isCsrfTokenValid("delete_image", $token)){
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage");
        $image = $repo->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $origine = $request->query->get("_from");

        $imgName = $image->getName();
        $catId = $image->getCategory()->getId();
        $imgPos = $image->getPosition();
        $imgPath = $image->getAbsolutePath();

        // remove img from db
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        // remove img from dirs
        unlink($imgPath . "/" . $this->container->getParameter("zoo_fototek_originals_dirname") . "/" . $imgName);
        unlink($imgPath . "/" . $this->container->getParameter("zoo_fototek_mds_dirname") . "/" . $imgName);
        unlink($imgPath . "/" . $this->container->getParameter("zoo_fototek_slides_dirname") . "/" . $imgName);
        unlink($imgPath . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/" . $imgName);

        // update positions
        $repo->moveUpImagesGroup($imgPos, $repo->getMaxPosition($catId), $catId);

        $this->container->get("session")->getFlashBag()->add("success", "L'image " . $imgName . " a bien été supprimée.");
        if($origine == "all"){
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_homepage"));
        } else {
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id", ["id"=>$catId]));
        }

    }

    public function setVisibleAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");
        if(!$token || !$csrfProvider->isCsrfTokenValid("set_visible_image", $token)){
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage");
        $image = $repo->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $origine = $request->query->get("_from");
        $em = $this->getDoctrine()->getManager();
        $em = $this->getDoctrine()->getManager();
        $image->setIsDisplayed(true);
        $em->persist($image);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Image " . $image->getName() . " mise à jour." );
        if($origine == "all"){
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_homepage"));
        } else {
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id", ["id"=>$image->getCategory()->getId()]));
        }
    }

    public function setInvisibleAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");
        if(!$token || !$csrfProvider->isCsrfTokenValid("set_invisible_image", $token)){
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage");
        $image = $repo->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $origine = $request->query->get("_from");
        $em = $this->getDoctrine()->getManager();
        $image->setIsDisplayed(false);
        $em->persist($image);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Image " . $image->getName() . " mise à jour." );
        if($origine == "all"){
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_homepage"));
        } else {
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id", ["id"=>$image->getCategory()->getId()]));
        }
    }

    public function allArchivesAction()
    {
        $imgs = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->findByIsArchived(true);
        $cats = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFCategory")->findAll();
        $thumbnailsDir = $this->container->getParameter("zoo_fototek_web_dir") . "/" . $this->container->getParameter("zoo_fototek_archives_dirname") . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/";
        return $this->render("AdminZooFototekBundle:Image:archives.html.twig",["images"=>$imgs,"categories"=>$cats, "thumb_dir"=>$thumbnailsDir]);
    }

    public function moveToArchivesAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $token = $request->query->get("_token");
        if(!$token || !$csrfProvider->isCsrfTokenValid("move_to_archives", $token)){
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage");
        $image = $repo->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $origine = $request->query->get("_from");

        $em = $this->getDoctrine()->getManager();
        $image->setIsArchived(true);
        $em->persist($image);
        $em->flush();
        $imgName = $image->getName();
        $imgPath = $image->getAbsolutePath();

        rename($imgPath . "/" . $this->container->getParameter("zoo_fototek_originals_dirname") . "/" . $imgName,$imgPath . "/" . $this->container->getParameter("zoo_fototek_archives_dirname") . "/" . $this->container->getParameter("zoo_fototek_originals_dirname") . "/" . $imgName);
        rename($imgPath . "/" . $this->container->getParameter("zoo_fototek_mds_dirname") . "/" . $imgName,$imgPath . "/" . $this->container->getParameter("zoo_fototek_archives_dirname") . "/" . $this->container->getParameter("zoo_fototek_mds_dirname") . "/" . $imgName);
        rename($imgPath . "/" . $this->container->getParameter("zoo_fototek_slides_dirname") . "/" . $imgName,$imgPath . "/" . $this->container->getParameter("zoo_fototek_archives_dirname") . "/" . $this->container->getParameter("zoo_fototek_slides_dirname") . "/" . $imgName);
        rename($imgPath . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/" . $imgName,$imgPath . "/" . $this->container->getParameter("zoo_fototek_archives_dirname") . "/" . $this->container->getParameter("zoo_fototek_thumbnails_dirname") . "/" . $imgName);

        $this->container->get("session")->getFlashBag()->add("success", "L'image " . $image->getName() . " déplacée en archives." );
        if($origine == "all"){
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_homepage"));
        } else {
            return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id", ["id"=>$image->getCategory()->getId()]));
        }
    }

    public function removeFromArchivesAction($id, Request $request)
    {
        //TODO
    }

    public function deleteFromArchiveAction($id, Request $request)
    {
        //TODO
    }

    public function moveupAction($id)
    {
        $image = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $pos = $image->getPosition();
        if($pos>1){
            $newPosition = $pos - 1;
            $this->_move($image, $newPosition);
        }
        return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id",["id"=>$image->getCategory()->getId()]));
    }

    public function movedownAction($id)
    {
        $image = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->find($id);
        if(!$image){
            throw new EntityNotFoundException();
        }
        $pos = $image->getPosition();
        $lastPos = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage")->getMaxPosition($image->getCategory()->getId());
        $newPosition = $pos + 1;
        if($newPosition <= $lastPos){
            $this->_move($image, $newPosition);
        }
        return $this->redirect($this->generateUrl("admin_zoo_fototek_image_get_by_cat_id",["id"=>$image->getCategory()->getId()]));
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

    private function _move(ZFImage $image, $newPosition)
    {
        $repo = $this->getDoctrine()->getRepository("AdminZooFototekBundle:ZFImage");
        $em = $this->getDoctrine()->getManager();

        if($newPosition < $image->getPosition()){

            $repo->moveDownImagesGroup($newPosition, $image->getPosition(), $image->getCategory()->getId());

        } else {
            $repo->moveUpImagesGroup($image->getPosition(), $newPosition, $image->getCategory()->getId());

        }
        $image->setPosition($newPosition);
        $em->persist($image);
        $em->flush();
    }
}
