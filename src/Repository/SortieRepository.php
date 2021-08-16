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
        $em= $this->getEntityManager();

        $query = $this->createQueryBuilder('s');               // SELECT * FROM sorties AS s

        if($datas['campus']){
            $query->join('s.campus', 'c')                       //INNER JOIN campus AS c ON campus.id = sortie.campus_id
            ->andWhere('c.id = :val')                                    //WHERE c.id = ?
            ->setParameter('val', $datas['campus']-> getId());      //?= $datas['campus'] -> id
        }

        if($datas['contient']){
            $query->andWhere('s.nom LIKE :val')                                   //WHERE s.nom LIKE ?
            ->setParameter('val', '%'.$datas['contient'].'%');          //? = $datas['contient']
        }

        if($datas['dateDeb'] && $datas['dateFin']) {
            $query->andWhere('s.dateHeureDebut BETWEEN :deb AND :fin')
                ->setParameter('deb', $datas['dateDeb'])
                ->setParameter('fin', $datas['dateFin']);
        }

        if ($datas['choices']){                                     //est ce qu'on a coché une case"
            if(in_array('orga', $datas['choices'])){
                $query->join('s.organisateur', 'o')       //La case "inscrit" fait-elle partie des cases cochées?
                ->andWhere('o.id = :val')
                    ->setParameter('val', $user-> getId());
            }
            if ($datas["choices"]){
                if(in_array('inscrit', $datas['choices'])){   //La case "inscrit" fait-elle partie des cases cochées?
                    $query->join('s.participant', 'p')
                        ->andWhere('p.id = :val')
                        ->setParameter('val', $user->getId());
                }



//                       ******************************************************************
//                       ******************** GROUPE CONCAT IMPOSSIBLE ********************
//                       ******************************************************************
//                if(in_array('noInscrit', $datas['choices'])){    //La case "inscrit" fait-elle partie des cases cochées?
//                    $subQuery = $em->createQuery('
//                        SELECT GROUP_CONCAT(p.id)
//                        FROM App\Entity\Sortie AS s
//                        INNER JOIN App\Entity\Participant AS p
//                    ');
//
//                    $query->andWhere(':val NOT IN ('.$subQuery-> getDQL().')')
//                        ->setParameter('val', $user->getId() );
//                }
            }
        }

        $response = $query->getQuery()
            ->getResult()
        ;
        return $response;
    }
}
