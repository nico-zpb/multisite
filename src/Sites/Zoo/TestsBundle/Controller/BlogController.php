<?php
/**
 * Created by PhpStorm.
 * User: Nicolas Canfrere
 * Date: 19/07/2014
 * Time: 10:50
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

namespace Sites\Zoo\TestsBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction($page=1)
    {
        $maxPage = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getNumPageForPublishedByDate(10);
        $posts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getAllPublishedOrderedByDate($page,10,$maxPage);
        $frontPost = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getALaUneZoo();
        if(!$frontPost){
            $frontPost = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getLastPost();
        }
        return $this->render("ZooTestsBundle:Blog:index.html.twig",
            [
                "posts"=>$posts,
                "frontPost"=>$frontPost,
                "currentPage"=>$page,
                "maxPage"=>$maxPage
            ]
        );
    }

    public function showAction($slug)
    {
        $post = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneBySlug($slug);
        if(!$post){
            throw $this->createNotFoundException();
        }
        return $this->render("ZooTestsBundle:Blog:show.html.twig", ["post"=>$post]);
    }

    public function showByCategoryAction($slug)
    {
        $category = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findOneBySlug($slug);
        if(!$category){
            throw $this->createNotFoundException();
        }
        $posts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getAllPublishedByCategoryAndOrderedByDate($slug);
        return $this->render("ZooTestsBundle:Blog:showByCategory.html.twig", ["posts"=>$posts,"category"=>$category]);
    }
}
