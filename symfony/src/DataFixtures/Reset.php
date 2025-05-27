<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class Reset extends Fixture
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        // Purge toutes les tables avant les tests
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        // Optionnel : ajouter des entités de base si nécessaire
        // Exemple : un rôle, un utilisateur test, etc.
    }
}
