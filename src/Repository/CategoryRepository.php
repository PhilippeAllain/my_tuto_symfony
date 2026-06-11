<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


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
        /*
        return new Paginator(
            $this->createQueryBuilder('c')
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit) // Nombre de résultats par page
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false), // Désactive le DISTINCT pour éviter les problèmes de pagination avec les jointures
                false // On ne compte pas les résultats pour éviter les problèmes de performance avec les jointures
        );
        */
    }

    /**
     * Summary of findAllWithCount[]
     * @return array
     */
    public function findAllWithCount(): array // On retourne un tableau
    {
        return $this->createQueryBuilder('c')
            ->select('NEW App\DTO\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))') /* On sélectionne les données que l'on veut retourner, on utilise un constructeur pour créer un objet de type CategoryWithCountDTO avec les données de la catégorie et le nombre de recettes liées à cette catégorie */
            ->leftJoin('c.recipes', 'r') // Liaison avec un left join pour récupérer les recettes liées à la catégorie
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
