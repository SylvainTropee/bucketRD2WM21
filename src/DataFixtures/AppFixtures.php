<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->text(15))
                ->setAuthor($faker->userName())
                ->setDateCreated($faker->dateTimeBetween('-6 year'))
                ->setDescription($faker->sentence())
                ->setIsPublished($faker->boolean(70));

            $manager->persist($wish);
        }
        $manager->flush();
    }
}
