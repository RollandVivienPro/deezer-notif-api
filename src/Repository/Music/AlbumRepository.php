<?php

namespace App\Repository\Music;

use App\Entity\Music\Album;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\RepoInterface\SpecListnotifInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository implements SpecListnotifInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function findOneForListNotifs(int $id){
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->leftJoin('a.artist','ar')
            ->addSelect('ar')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        ;        
    }
    // /**
    //  * @return Album[] Returns an array of Album objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
