<?php

namespace Sites\Admin\Zoo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($page = 1)
    {
        $categories = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBCategory")->findAllAlphaOrdered();
        $maxPage = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getNumPageForPublishedByDate(10);
        if($categories){
            //$draftPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findByIsDraft(true);
            $draftPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getAllDraftOrderedByDate();
            $tags = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBTag")->findAllAlphaOrdered();
            //TODO pagination des posts : http://doctrine-orm.readthedocs.org/en/latest/reference/query-builder.html#limiting-the-result
            //$publishedPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findByIsPublished(true);
            $publishedPosts = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->getAllPublishedOrderedByDate($page,10,$maxPage);
            $front = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFront(true);
            $frontBN = $this->getDoctrine()->getRepository("AdminZooBlogBundle:ZBPost")->findOneByIsFrontBN(true);
        } else {
            $tags = [];
            $draftPosts = [];
            $publishedPosts = [];
            $front = null;
            $frontBN = null;
        }

        return $this->render('AdminZooBlogBundle:Default:index.html.twig',
            [
                "drafts"=>$draftPosts,
                "published"=>$publishedPosts,
                "categories"=>$categories,
                "alaune"=>$front,
                "alaunebn"=>$frontBN,
                "tags"=>$tags,
                "currentPage"=>$page,
                "maxPage"=>$maxPage,
            ]
        );
    }
}
