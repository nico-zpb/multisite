<?php

namespace Sites\Admin\Zoo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        if($categories){
            $draftPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findByIsDraft(true);
            //TODO pagination des posts : http://doctrine-orm.readthedocs.org/en/latest/reference/query-builder.html#limiting-the-result
            $publishedPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findByIsPublished(true);
            $front = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFront(true);
            $frontBN = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFrontBN(true);
        } else {
            $draftPosts = [];
            $publishedPosts = [];
            $front = null;
            $frontBN = null;
        }

        return $this->render('AdminZooBlogBundle:Default:index.html.twig', ["drafts"=>$draftPosts, "published"=>$publishedPosts, "categories"=>$categories, "alaune"=>$front, "alaunebn"=>$frontBN]);
    }
}
