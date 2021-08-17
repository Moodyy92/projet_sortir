<?php

namespace App\Repository;

use App\Entity\PhotoDeProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhotoDeProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoDeProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoDeProfil[]    findAll()
 * @method PhotoDeProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoDeProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoDeProfil::class);
    }

    // /**
    //  * @return PhotoDeProfil[] Returns an array of PhotoDeProfil objects
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
    public function findOneBySomeField($value): ?PhotoDeProfil
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
