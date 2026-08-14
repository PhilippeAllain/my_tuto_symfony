<?php

namespace App\DataFixtures;

use App\Entity\Quantity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Ingredient;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $ingredients = array_map(fn(string $name) => (new Ingredient())
        ->setName($name)
        ->setSlug(strtolower($this->slugger->slug($name))), [
            'Farine', 'Sucre', 'Beurre', 'Oeufs', 'Lait', 'Chocolat', 'Vanille', 'Levure chimique',
            'Sel', 'Huile d\'olive', 'Ail', 'Oignon', 'Tomates', 'Poivrons', 'Courgettes',
            'Carottes', 'Pommes de terre', 'Riz', 'Pâtes', 'Fromage', 'Cannelle', 'Noix de muscade', 'Miel', 'Citron', 'Orange', 'Fraise', 'Framboise', 'Myrtille',
        ]);

        $units = ['g', 'kg', 'ml', 'l', 'c. à soupe', 'c. à café', 'pincée', 'tranche', 'feuille', 'bouquet'];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categories = ['Plat chaud', 'Dessert', 'Entrée', 'Goûter'];
        $categoryObjects = [];
        foreach ($categories as $c) {
            $category = (new Category())
                ->setName($c)
                ->setSlug($this->slugger->slug($c))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));

            $manager->persist($category);
            $this->addReference($c, $category);
            $categoryObjects[] = $category;
        }

        for ($i = 0; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = new \App\Entity\Recipe();
            $recipe->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setContent($faker->paragraph(10, true))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
                ->setUser($this->getReference('USER' . $faker->numberBetween(1, 10), User::class))
                ->setDuration($faker->numberBetween(1, 120))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));

                $selectedIngredientKeys = array_rand($ingredients, $faker->numberBetween(2, 5));
                foreach ((array) $selectedIngredientKeys as $selectedIngredientKey) {
                    $ingredient = $ingredients[$selectedIngredientKey];
                    $quantity = (new Quantity())
                        ->setQuantity($faker->numberBetween(1, 250))
                        ->setUnit($faker->randomElement($units))
                        ->setIngredient($ingredient);

                    $recipe->addQuantity($quantity);
                }
            $manager->persist($recipe);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
