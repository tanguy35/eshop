<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */
    
    public function findWithSearch($search)
    {

        $query = $this->createQueryBuilder('p');
        if($search->getMinPrice()){
            $query = $query->andWhere('p.price > '.$search->getMinPrice()*100);

        }

        if($search->getMaxPrice()){
            $query = $query->andWhere('p.price < '.$search->getMaxPrice()*100);

        }

        //tags
        if($search->getTags()){
            $query = $query->andWhere('p.tags like :val')
                           ->setParameter('val', "%{$search->getTags()}%");

        }

        //categories
        if($search->getCategories()){
            $query = $query->join('p.category', 'c')//jointure inner entre table product et categories
                           ->andWhere('c.id IN (:categories)')
                           ->setParameter('categories', $search->getCategories());
        }
        return $query->getQuery()->getResult();
        
    }
    

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
