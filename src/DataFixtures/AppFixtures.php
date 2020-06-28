<?php

namespace App\DataFixtures;

use App\Entity\Ship;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 *
 * @package App\DataFixtures
 * @author Christian Ruppel < post@christianruppel.de >
 */
class AppFixtures extends Fixture
{

    /**
     * @var array[]
     */
    private $ships = [[
        'size' => 5,
        'title' => 'Carrier'
    ], [
        'size' => 4,
        'title' => 'Battleship'
    ], [
        'size' => 3,
        'title' => 'Cruiser'
    ], [
        'size' => 3,
        'title' => 'Submarine'
    ], [
        'size' => 2,
        'title' => 'Destroyer'
    ]];


    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->ships as $ship) {
            $newShip = new Ship();
            $newShip->setSize($ship['size']);
            $newShip->setTitle($ship['title']);

            $manager->persist($newShip);
        }

        $manager->flush();
    }
}
