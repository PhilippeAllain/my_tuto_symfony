<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

  public function paginateRecipes(int $page, int $limit): PaginationInterface
  {

  return $this->paginator->paginate(
        $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c')
            ->orderBy('r.id', 'ASC')
            ->getQuery(),
        $page,
        $limit,
        [
            'distinct' => false,
            'sortFieldAllowed' => ['r.id', 'r.title']
        ]
    );
  }
  
  public function findTotalDuration(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Summary of findWithDurationLowerThan
     * @param int $duration
     * @return array Recipe[]
     */
    public function findWithDurationLowerThan(int $duration): array // Méthode personnalisée qui crée une requête pour trouver des recettes avec une durée inférieure à une valeur donnée
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c') // Sélectionne à la fois les recettes et leurs catégories associées
            ->andWhere('r.duration < :duration')
            ->setParameter('duration', $duration)
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c') // Jointure avec la table des catégories
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
