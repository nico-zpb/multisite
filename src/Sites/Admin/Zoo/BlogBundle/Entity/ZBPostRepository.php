<?php

namespace Sites\Admin\Zoo\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ZBBlogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ZBPostRepository extends EntityRepository
{
    public function countPublished()
    {
        return $this->createQueryBuilder("p")->select("COUNT(p)")->where("p.isPublished = :isPublished")->setParameter("isPublished", true)->getQuery()->getSingleScalarResult();
    }

    public function getNumPageForPublishedByDate($max = 10)
    {
        $numPost = $this->countPublished();
        if($numPost>0){
            $maxPage = ceil($numPost/$max);
        } else {
            $maxPage = 1;
        }
        return $maxPage;
    }

    public function getAllPublishedOrderedByDate($page = 1, $max = 10, $maxPage = null)
    {
        if(!$maxPage){
            $maxPage = $this->getNumPageForPublishedByDate($max);
        }

        if($page>$maxPage){
            $page = $maxPage;
        }
        $offset = (($page - 1) * $max);
        $qb = $this->createQueryBuilder("p")
            ->where("p.isPublished = :isPublished")
            ->orderBy("p.publishedAt", "DESC")
            ->setFirstResult($offset)
            ->setMaxResults($max)
            ->setParameter("isPublished", true);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getAllDraftOrderedByDate()
    {

        $qb = $this->createQueryBuilder("t")
            ->where("t.isDraft = :isDraft")
            ->orderBy("t.createdAt", "DESC")
            ->setParameter("isDraft", true);
        $query = $qb->getQuery();
        return $query->getResult();
    }


    public function getAllPublishedByCategoryAndOrderedByDate($id)
    {
        $query = $this->_em->createQuery("SELECT t FROM AdminZooBlogBundle:ZBPost t JOIN t.category c WHERE c.id=:id AND t.isPublished=1 ORDER BY t.publishedAt ASC");
        $query->setParameter("id", $id);
        return $query->getResult();
    }
}
