<?php

namespace Sites\Admin\Common\MediatekBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function findAllAlphaOrdered()
    {
        $qb = $this->createQueryBuilder("t")->orderBy("t.name", "ASC");
        return $qb->getQuery()->getResult();
    }
}
