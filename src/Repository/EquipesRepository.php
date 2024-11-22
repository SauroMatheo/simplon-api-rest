<?php

namespace App\Repository;

use App\Entity\Equipes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipes>
 */
class EquipesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipes::class);
    }

    /**
    * Limite le nombre de résultats d'équipes.
    * @param int $limit
    * @param ?int $offset = 0
    * @return Equipes[] Renvoie un array d'Equpes
    */
    public function findLimit(
        int $limit,
        ?int $offset = 0
        ): array
    {
        return $this->createQueryBuilder('e')
            ->setMaxResults(max(1,$limit))
            ->setFirstResult(max(0, $offset))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * Permet de rechercher des équipes en fonction de leur nom et/ou score minimal/maximal.
    * Lorsqu'un paramètre est vide, il n'est pas pris en compte, pour faciliter l'utilisation.
    * @param string     $nom
    * @param ?int       $scoreMin
    * @param ?int       $scoreMax
    * @param ?int       $limit
    * @param ?int       $offset = 0
    * @return Equipes[] Returns an array of Articles objects
    */
    public function findSearch(
        string $nom,
        ?int $scoreMin,
        ?int $scoreMax,
        ?int $limit,
        ?int $offset = 0
        ): array
    {
        $query = $this->createQueryBuilder('e');

        if (!empty($nom)) {
            $query = $query
            ->andWhere('e.nom LIKE :nom')
            ->setParameter('nom', '%'.$nom.'%');
        }

        if (!empty($scoreMin)) {
            $query = $query
            ->andWhere('e.score >= :scoreMin')
            ->setParameter('scoreMin', $scoreMin);
        }

        if (!empty($scoreMax)) {
            $query = $query
            ->andWhere('e.score <= :scoreMax')
            ->setParameter('scoreMax', $scoreMax);
        }        
        
        if (!empty($limit)) {
            $query = $query
            ->setMaxResults(max(1,$limit));
        }

        $query = $query->setFirstResult(max(0, $offset));

        return $query->getQuery()->getResult();
    }


    // public function findById($id): ?Equipes
    // {
    //     return $this->createQueryBuilder('e')
    //         ->andWhere('e.id = :id')
    //         ->setParameter('id', $id)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }

    //    /**
    //     * @return Equipes[] Returns an array of Equipes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipes
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
