<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\PaginationDTO;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

class RecipesController extends AbstractController
{
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepository, 
        #[MapQueryString]
        ?PaginationDTO $paginationDTO = null
        )
    {

        $recipes = $recipeRepository->paginateRecipes($paginationDTO?->page, $paginationDTO->limit);
        return $this->json($recipes, 200, [], [
            'groups' => 'recipes.index']
        );
    }

    

    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS])]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
            ]);
    }

    #[Route('/api/recipes', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(
            serializationContext: ['groups' => ['recipes.create']]
        )]
        Recipe $recipe,
        EntityManagerInterface $entityManager,
        )
        {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->persist($recipe);
        $entityManager->flush();
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }
}