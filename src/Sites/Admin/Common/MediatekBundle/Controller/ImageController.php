<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 20/07/2014
 * Time: 15:24
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


use Sites\Admin\Common\MediatekBundle\Entity\Document;
use Sites\Admin\Common\MediatekBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImageController extends Controller
{
    public function newAction()
    {
        $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
        $img = new Document();
        $img->setDocType("image");

        return $this->render("AdminCommonMediatekBundle:Image:new.html.twig", ["form_errors"=>[],"image"=>$img,"tags"=>$tags]);
    }

    public function createAction(Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_image_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("new_image", $form["_token"])){
            throw new AccessDeniedException();
        }

        $errors = [];

        if($form["doctype"] != "image"){
            $errors[] = "Le type de document est incorrect.";
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get("new_image_form")["file"];
        if(!$file instanceof UploadedFile || !$file->isValid()){
            $errors[] = "Pas de fichier à télécharger.";
        }

        $allowedMimes = $this->container->getParameter("mediatek_allowed_mime_types");
        if(!in_array($file->getMimeType(), $allowedMimes)){
            $errors[] = "Le fichier n'est pas du bon type";
        }

        if(!empty($form["filename"]) && preg_replace('/[a-zA-Z0-9._-]/','',$form["filename"]) !== ""){
            $errors[] = "Le champ 'nom' contient des caratères interdits.";
        }

        if(!empty($form["title"])){
            $form["title"] = trim(strip_tags(preg_replace('/\s\s+/',' ',$form["title"])));
        }

        if(!empty($form["copyright"])){
            $form["copyright"] = " &copy; " . trim(strip_tags(preg_replace('/\s\s+/',' ',$form["copyright"])));
        } else {
            $form["copyright"] = " &copy; ZooParc de Beauval";
        }

        $img = new Document();
        $img->setDocType("image");
        $img->setTitle($form["title"]);
        $img->setCopyright($form["copyright"]);
        if(!empty($form["filename"])){
            $img->setFilename($form["filename"]);
        }

        if(array_key_exists("tags", $form)){
            foreach($form["tags"] as $k=>$v){
                $t = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($v);
                if(!$t){
                    throw $this->createNotFoundException();
                }
                $img->addTag($t);
            }
        }
        $em = $this->getDoctrine()->getManager();
        if(!empty($form["newtags"]) ){
            $form["newtags"] = trim(preg_replace('/\s\s+/'," ",$form["newtags"]));
            $tags = explode(";", $form["newtags"]);

            foreach($tags as $k=>$v){
                $v = trim(preg_replace('/\s\s+/'," ",$v));
                if($v){
                    if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?, _-]/",'',$v) == ""){
                        if(!$this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findOneByName($v)){
                            $tag = new Tag();
                            $tag->setName($v);
                            $em->persist($tag);
                            $img->addTag($tag);
                        }
                    }
                }
            }
            $em->flush();
        }



        if($errors){
            $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
            return $this->render("AdminCommonMediatekBundle:Image:new.html.twig", ["form_errors"=>$errors,"image"=>$img,"tags"=>$tags]);
        }

        $suffix = "";

        if(!empty($form["date"])){
            $date = new \DateTime("now", new \DateTimeZone("Europe/Paris"));
            $suffix .= "_" . $date->format("d-m-y") ;
        }
        $size = getimagesize($file->getRealPath());
        if(!empty($form["dims"])){
            $suffix .= "_" . $size[0] . "x" . $size[1];
        }
        if(!empty($form["filename"])){
            $name = $form["filename"] . $suffix . "." . $file->guessExtension();
        } else {
            $tmp = $file->getClientOriginalName();
            if(false !== $pos = strrpos($tmp, ".")){
                $tmp = substr($tmp, 0, $pos);
            }
            $name = $tmp . $suffix . "." . $file->guessExtension();
        }
        $img->setWidth($size[0]);
        $img->setHeight($size[1]);
        $img->setFilename($name);
        $img->setMime($file->getMimeType());
        $img->setExtension($file->guessExtension());
        $img->setAbsolutePath($this->container->getParameter("mediatek_images_base_dir"));
        $img->setWebPath($this->container->getParameter("mediatek_images_web_dir"));
        $file->move($this->container->getParameter("mediatek_images_base_dir") , $name);

        $em->persist($img);
        $em->flush();
        $this->makeThumbnail(
            $img,
            $this->container->getParameter("mediatek_images_thumbnails_dirname"),
            $this->container->getParameter("mediatek_images_thumbnail_width"),
            $this->container->getParameter("mediatek_images_thumbnail_height")
        );

        $this->container->get("session")->getFlashBag()->add("success", "Votre document à bien été enregistré.");
        return $this->redirect($this->generateUrl("admin_common_mediatek_homepage"));
    }

    public function editAction($id, Request $request)
    {
        $token = $request->query->get("_token");
        $csrfProvider = $this->container->get("form.csrf_provider");
        if(!$token || !$csrfProvider->isCsrfTokenValid("edit_image", $token)){
            throw new AccessDeniedException();
        }

        $img = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Document")->find($id);
        if(!$img || $img->getDocType() != "image"){
            throw $this->createNotFoundException();
        }
        $img->setCopyright(trim(str_replace("&copy;", "", $img->getCopyright())));
        $img->setFilename(trim(str_replace("." . $img->getExtension(),"",$img->getFilename())));
        $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
        return $this->render("AdminCommonMediatekBundle:Image:edit.html.twig", ["form_errors"=>[],"image"=>$img, "tags"=>$tags]);
    }

    public function updateAction($id, Request $request)
    {
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("update_image_form");

        if(empty($form["_token"]) || !$csrfProvider->isCsrfTokenValid("update_image", $form["_token"])){
            throw new AccessDeniedException();
        }

        $img = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Document")->find($id);
        if(!$img || $img->getDocType() != "image"){
            throw $this->createNotFoundException();
        }
        $imgCopyright = trim(str_replace("&copy;","",$img->getCopyright()));
        $errors = [];
        $filename = $img->getFilename();
        if(!empty($form["filename"]) && $form["filename"] != trim(str_replace(".".$img->getExtension(),"",$img->getFilename()))){
            if(preg_replace('/[a-zA-Z0-9._-]/','',$form["filename"]) !== ""){
                $errors[] = "Le champ 'nom' contient des caratères interdits.";
            }

            $img->setFilename($form["filename"]);
        }

        if(!empty($form["title"]) && $form["title"] != $img->getTitle()){
            $form["title"] = trim(strip_tags(preg_replace('/\s\s+/',' ',$form["title"])));
            $img->setTitle($form["title"]);
        }

        if(!empty($form["copyright"]) && $form["copyright"] != $imgCopyright){
            $form["copyright"] = " &copy; " . trim(strip_tags(preg_replace('/\s\s+/',' ',$form["copyright"])));
            $img->setCopyright($form["copyright"]);
        } elseif(empty($form["copyright"])) {
            $form["copyright"] = " &copy; ZooParc de Beauval";
            $img->setCopyright($form["copyright"]);
        }

        if(array_key_exists("associatedtags", $form)){
            foreach($form["associatedtags"] as $k=>$v){
                if($v){
                    $t = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($v);
                    if(!$t){
                        throw $this->createNotFoundException();
                    }
                    $img->removeTag($t);
                }
            }
        }

        if(array_key_exists("tags", $form)){
            foreach($form["tags"] as $k=>$v){
                if($v){
                    $t = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->find($v);
                    if(!$t){
                        throw $this->createNotFoundException();
                    }
                    $img->addTag($t);
                }
            }
        }


        $em = $this->getDoctrine()->getManager();
        if(!empty($form["newtags"]) ){
            $form["newtags"] = trim(preg_replace('/\s\s+/'," ",$form["newtags"]));
            $tags = explode(";", $form["newtags"]);

            foreach($tags as $k=>$v){
                $v = trim(preg_replace('/\s\s+/'," ",$v));
                if($v){
                    if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?, _-]/",'',$v) == ""){
                        if(!$this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findOneByName($v)){
                            $tag = new Tag();
                            $tag->setName($v);
                            $em->persist($tag);
                            $img->addTag($tag);
                        }
                    }
                }
            }
            $em->flush();
        }

        if($errors){
            $tags = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Tag")->findAllAlphaOrdered();
            return $this->render("AdminCommonMediatekBundle:Image:edit.html.twig", ["form_errors"=>$errors,"image"=>$img,"tags"=>$tags]);
        }

        $suffix = "";

        if(!empty($form["date"])){
            $date = new \DateTime("now", new \DateTimeZone("Europe/Paris"));
            $suffix .= "_" . $date->format("d-m-y") ;
        }

        if(!empty($form["dims"])){
            $suffix .= "_" . $img->getWidth() . "x" . $img->getHeight();
        }

        $img->setFilename($img->getFilename() . $suffix . "." . $img->getExtension());
        if($img->getFilename() != $filename){
            rename($img->getAbsolutePath()."/".$filename, $img->getAbsolutePath()."/".$img->getFilename());
            rename($img->getAbsolutePath()."/". $this->container->getParameter("mediatek_images_thumbnails_dirname") . "/" .$filename, $img->getAbsolutePath()."/". $this->container->getParameter("mediatek_images_thumbnails_dirname") . "/".$img->getFilename());

        }
        $em->persist($img);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "L'image " . $img->getFilename() . " a bien été mise à jour dans la médiathèque.");
        return $this->redirect($this->generateUrl("admin_common_mediatek_homepage"));
    }

    public function deleteAction($id, Request $request)
    {
        $token = $request->query->get("_token");
        $csrfProvider = $this->container->get("form.csrf_provider");
        if(!$token || !$csrfProvider->isCsrfTokenValid("delete_image", $token)){
            throw new AccessDeniedException();
        }

        $img = $this->getDoctrine()->getRepository("AdminCommonMediatekBundle:Document")->find($id);
        if(!$img || $img->getDocType() != "image"){
            throw $this->createNotFoundException();
        }
        $filename = $img->getFilename();

        $pathToOriginal = $img->getAbsolutePath() . "/" . $img->getFilename();
        $pathToThumbnail = $img->getAbsolutePath() . "/" . $this->container->getParameter("mediatek_images_thumbnails_dirname") . "/" . $img->getFilename();

        unlink($pathToOriginal);
        unlink($pathToThumbnail);

        $em = $this->getDoctrine()->getManager();

        $em->remove($img);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "L'image " . $filename . " a bien été supprimée de la médiathèque.");
        return $this->redirect($this->generateUrl("admin_common_mediatek_homepage"));
    }

    private function makeThumbnail(Document $image, $dirname = "originales", $width = 300, $height = 450, $quality = 75)
    {
        if($image->getDocType() != "image"){
            return false;
        }

        $iniWidth = $image->getWidth();
        $iniHeight = $image->getHeight();

        $gdImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($gdImage, 255,255,255);
        imagefill($gdImage,0,0, $white);
        if($image->getMime() == "image/jpeg"){
            $copyImage = imagecreatefromjpeg($image->getAbsolutePath() . "/" . $image->getFilename());
        } elseif($image->getMime() == "image/gif") {
            $copyImage = imagecreatefromjpeg($image->getAbsolutePath() . "/" . $image->getFilename());
        } elseif($image->getMime() == "image/png") {
            $copyImage = imagecreatefromjpeg($image->getAbsolutePath() . "/" . $image->getFilename());
        } else {
            return false;
        }
        $result = false;
        $thumbIsLandscape = ( $width >= $height ) ? true : false;

        if($iniWidth>=$iniHeight){
            // paysage
            if($thumbIsLandscape){
                $newHeight = $width * ($iniHeight/$iniWidth);
                if($newHeight>=$height){
                    //ok
                    $result = imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
                } else {
                    $newWidth = $height * ($iniWidth/$iniHeight);
                    $result = imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
                }
            } else {
                $newWidth = $height * ($iniWidth/$iniHeight);
                $result = imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
            }

        } else {
            //portrait
            if($thumbIsLandscape){
                $newHeight = $width * ($iniHeight/$iniWidth);
                $result = imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
            } else {
                $newWidth = $height * ($iniWidth/$iniHeight);
                if($newWidth>=$width){
                    //ok
                    $result = imagecopyresampled($gdImage, $copyImage, ($width-$newWidth)/2,0,0,0, $newWidth, $height, $iniWidth, $iniHeight);
                } else {
                    $newHeight = $width * ($iniHeight/$iniWidth);
                    $result = imagecopyresampled($gdImage, $copyImage, 0,($height-$newHeight)/2,0,0, $width, $newHeight, $iniWidth, $iniHeight);
                }
            }
        }
        if($result){
            if($image->getMime() == "image/jpeg"){
                $result = imagejpeg($gdImage, $image->getAbsolutePath() . "/" . $dirname . "/" . $image->getFilename(), $quality);
            } elseif($image->getMime() == "image/gif") {
                $result = imagegif($gdImage, $image->getAbsolutePath() . "/" . $dirname . "/" . $image->getFilename());
            } else {
                $result = imagepng($gdImage, $image->getAbsolutePath() . "/" . $dirname . "/" . $image->getFilename(), $quality);
            }
        }


        imagedestroy($gdImage);
        return $result;

    }
}
