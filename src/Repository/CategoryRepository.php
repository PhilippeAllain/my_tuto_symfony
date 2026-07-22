<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\DTO\CategoryWithCountDTO;


/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Category::class);
    }

    public function paginateCategories(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('c')
                ->orderBy('c.name', 'ASC')
                ->getQuery(),
            $page,
            2,
            [
                'distinct' => false, // Désactive le DISTINCT pour éviter les problèmes de pagination avec les jointures
                'sortFieldAllowed' => ['c.id', 'c.name'] // Spécifie les champs autorisés
            ]
        );

    }

    /**
     * @return CategoryWithCountDTO[] Returns an array of CategoryWithCountDTO objects
     */
    public function findAllWithCount(): array // On retourne un tableau
    {
        return $this->createQueryBuilder('c')
            ->select('New App\\DTO\\CategoryWithCountDTO(c.id, c.name, COUNT(r.id))') // On sélectionne les champs nécessaires pour créer un objet CategoryWithCountDTO
            ->leftJoin('c.recipes', 'r') // On fait une jointure gauche avec la table des recettes
            ->groupBy('c.id') // Groupement par id de catégorie pour éviter les doublons
            ->getQuery() // Pour générer la requête
            ->getResult(); // On retourne le résultat de la requête
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
