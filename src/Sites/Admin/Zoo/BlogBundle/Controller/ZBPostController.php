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
use Sites\Admin\Zoo\BlogBundle\Entity\ZBTag;
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
        $tags = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->findAllAlphaOrdered();
        $post = new ZBPost();
        return $this->render("AdminZooBlogBundle:ZBPost:new.html.twig", ["form_errors"=>[], "categories"=>$categories, "post"=>$post, "tags"=>$tags]);
    }

    public function createAction(Request $request)
    {
        /*var_dump($request->request->all());
        die();*/
        $csrfProvider = $this->container->get("form.csrf_provider");
        $form = $request->request->get("new_post_form");
        if(!$form["_token"] || !$csrfProvider->isCsrfTokenValid("new_post",$form["_token"])){
            throw new AccessDeniedException();
        }

        $errors = [];
        $cat = null;
        if(empty($form["category"]) || false === preg_match('/^[0-9]+$/', $form["category"])){
            $errors[] = "Vous devez associer une catégorie à votre article";
        }
        if(!empty($form["category"]) ){
            $cat = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->find($form["category"]);
            if(!$cat){
                $errors[] = "La catégorie associée n'existe pas";
            }
        }
        $form["title"] = trim(preg_replace('/\s\s+/', '', $form["title"]));
        if(empty($form["title"])){
            $errors[] = "Votre article doit avoir un titre.";
        }
        if(!empty($form["title"])){

            if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?.,; _-]/",'',$form["title"]) != ""){
                $errors[] = "Le titre de votre article contient des caractères interdits.";
            }

        }
        if(!empty($form["slug"]) && preg_replace("/[a-z0-9-]/",'',$form["slug"]) != ""){
            $errors[] = "Le champs 'alias' contient des caractères interdits.";
        }
        if($form["submit"] == "save_publish" || $form["submit"] == "save_front" || $form["submit"] == "save_delayed"){
            if(empty($form["body"])){
                $errors[] = "Vous ne pouvez pas publier un article sans contenu.";
            }
            if(empty($form["excerpt"])){
                $errors[] = "Vous ne pouvez pas publier un article sans résumé.";
            }
        }
        if($form["submit"] == "save_front"){
            if(empty($form["isfront"]) && empty($form["isfrontbn"])){
                $errors[] = "Précisez à quelle 'une' vous voulez mettre votre article";
            }
        }
        $em = $this->getDoctrine()->getManager();
        $post = new ZBPost();
        //TODO validation des mots cles
        if(array_key_exists("tags", $form)){
            foreach($form["tags"] as $k=>$v){
                $t = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->find($v);
                if(!$t){
                    throw $this->createNotFoundException();
                }
                $post->addTag($t);
            }
        }

        if(!empty($form["newtags"])){
            $form["newtags"] = trim(preg_replace('/\s\s+/'," ",$form["newtags"]));
            $tags = explode(";", $form["newtags"]);

            foreach($tags as $k=>$v){
                $v = trim(preg_replace('/\s\s+/'," ",$v));
                if($v){
                    if(preg_replace("/[a-zA-Z0-9éèêàçùûëïôâ'!?, _-]/",'',$v) == ""){
                        $tag = new ZBTag();
                        $tag->setName($v);
                        $em->persist($tag);
                        $post->addTag($tag);
                    }
                }
            }
            $em->flush();
        }

        //TODO validation date différé


        $post->setTitle($form['title']);
        if($form["slug"]){
            $post->setSlug($form["slug"]);
        }
        $post->setBody($form["body"]);
        $post->setExcerpt($form["excerpt"]);
        if($cat){
            $post->setCategory($cat);
        }


        if($errors){
            $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
            return $this->render("AdminZooBlogBundle:ZBPost:new.html.twig", ["form_errors"=>$errors, "categories"=>$categories, "post"=>$post]);
        }
        $em = $this->getDoctrine()->getManager();
        if($form["submit"] == "save_publish"){
            $post->setIsDraft(false);
            $post->setIsPublished(true);
            $post->setPublishedAt(new \DateTime("now", new \DateTimeZone("Europe/Paris")));
        }

        if($form["submit"] == "save_front"){
            $post->setIsDraft(false);
            $post->setIsPublished(true);
            $post->setPublishedAt(new \DateTime("now", new \DateTimeZone("Europe/Paris")));
            if(!empty($form["isfront"])){
                $lastFrontPost = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFront(true);
                if($lastFrontPost){
                    $lastFrontPost->setIsFront(false);
                    $em->persist($lastFrontPost);
                }
                $post->setIsFront(true);
            }
            if(!empty($form["isfrontbn"])){
                $lastFrontBNPost = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFrontBN(true);
                if($lastFrontBNPost){
                    $lastFrontBNPost->setIsFrontBN(false);
                    $em->persist($lastFrontBNPost);
                }
                $post->setIsFrontBN(true);
            }

        }

        if($form["submit"] == "save_delayed"){
            $post->setIsDraft(false);
            $post->setIsDelayed(true);
        }

        $em->persist($post);
        $em->flush();

        $this->container->get("session")->getFlashBag()->add("success", "Votre article a bien été sauvegardé");
        return $this->redirect($this->generateUrl("admin_zoo_blog_homepage"));
    }

    public function editAction($id, Request $request)
    {

        $post = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->find($id);
        if(!$post){
            throw $this->createNotFoundException();
        }

        return $this->render("AdminZooBlogBundle:ZBPost:edit.html.twig", ["post"=>$post]);
    }


}
