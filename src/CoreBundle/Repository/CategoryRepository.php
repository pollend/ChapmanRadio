<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 5/29/17
 * Time: 10:50 PM
 */

namespace CoreBundle\Repository;


use CoreBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CategoryRepository extends EntityRepository
{
    /**
     * @param string $tag
     */
    public function getOrCreateCategory($category)
    {

        $em = $this->getEntityManager();

        $result = null;
        $qb =  $this->createQueryBuilder('t');
        try {
            $result = $qb->where($qb->expr()->eq("category", ":category"))
                ->setParameter("category", $category)
                ->getQuery()
                ->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $result = new Category();
            $result->setCategory($category);

            $em->persist($result);
            $em->flush();
        }
        return $result;
    }

    /**
     * @param $category
     * @param int $limit
     * @return array
     */
    public function findCategory($category,$limit = -1)
    {
        $qb = $this->createQueryBuilder("t");
        $categories = $qb->where($qb->expr()->like('t.category',':category'))
            ->setParameter("category",'%'. $category.'%')
            ->getQuery();
        if($limit > 0)
            $categories->setMaxResults($limit);
        return $categories->getResult();
    }

    public function getCategory($category)
    {
        return $this->findOneBy(["category" => $category]);
    }
}
