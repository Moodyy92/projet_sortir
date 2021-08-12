<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */

    public function search($datas, $user): array
    {
        $query = $this->createQueryBuilder('s')                    // SELECT * FROM sorties AS s
            ->join('s.campus', 'c')                           //INNER JOIN campus AS c ON campus.id = sortie.campus_id
            ->andWhere('c.id = :val')                                   //WHERE c.id = ?
            ->setParameter('val', $datas['campus']-> getId());      //?= $datas['campus'] -> id

        if($datas['contient']){
            $query->andWhere('s.nom LIKE :val')                                   //WHERE s.nom LIKE ?
                ->setParameter('val', '%'.$datas['contient'].'%');      //? = $datas['contient']
        }

        if($datas["choices"]){
            $query->join('s.organisateur', 'o')
                ->andWhere('o.id = :val')
                ->setParameter('val', $user-> getId());
        }

         $response = $query->getQuery()
            ->getResult()
        ;
        return $response;
    }
}
