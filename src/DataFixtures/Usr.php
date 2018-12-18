<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class Usr extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setName('Priceless widget!');
        $product->setPrice(14.50);
        $product->setDescription('Ok, I guess it *does* have a price');
        $manager->persist($product);

        // add more products

        $manager->flush();
    }
}
