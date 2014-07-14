<?php

namespace Sites\Admin\Zoo\FototekBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ZFImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ZFImageRepository extends EntityRepository
{
    public function getAllByCategory($id)
    {
        $query = $this->_em->createQuery("SELECT i FROM AdminZooFototekBundle:ZFImage i JOIN i.category c WHERE c.id=:id ORDER BY i.position ASC");
        $query->setParameter('id', $id);
        return $query->getResult();
    }

    public function getMaxPosition($id)
    {
        $query = $this->_em->createQuery("SELECT MAX(i.position) AS maxi FROM AdminZooFototekBundle:ZFImage i JOIN i.category c WHERE  c.id=:id");
        $query->setParameter('id', $id);
        return $query->getSingleScalarResult();
    }
}
