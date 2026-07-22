<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    #[Route('/api/recipes')]
    public function index(RecipeRepository $recipeRepository, Request $request, SerializerInterface $serializer)
    {

        $recipes = $recipeRepository->paginateRecipes($request->query->getInt('page', 1), $request->query->getInt('limit', 10));
        return $this->json($recipes, 200, [], [
            'groups' => 'recipes.index']
        );
    }

    

    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
            ]);
    }
}