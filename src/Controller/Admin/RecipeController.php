<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use App\Security\Voter\RecipeVoter;

#[Route("/admin/recettes", name: 'admin.recipe.')]
// #[IsGranted('ROLE_ADMIN')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 4;
        $recipes = $repository->paginateRecipes($page, $limit);
        $maxPages = ceil($recipes->getTotalItemCount() / $limit);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'maxPages' => $maxPages,
            'page' => $page
        ]);
    }
    
    #[Route('/create', name: 'create')]
    #[IsGranted(RecipeVoter::CREATE)]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RecipeVoter::EDIT, subject:'recipe')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper): Response
    {
        //dd($helper->asset($recipe, 'thumbnailFile'));
        // dd($recipe);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        // dd($recipe);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Recette modifiée avec succès !');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('admin.recipe.index');
    }

    /*
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9\-]+', 'id' => '\d+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        //dd($recipe);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', [
                'id' => $recipe->getId(),
                'slug' => $recipe->getSlug()
            ]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
    */
}
