<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Category;
use App\Entity\User;
class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

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
