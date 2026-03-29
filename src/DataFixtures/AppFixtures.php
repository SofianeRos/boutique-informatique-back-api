<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Création d'un faux compte Client (Celui qui n'a pas le droit de supprimer)
        $user = new User();
        $user->setEmail('client@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'azerty'));
        $user->setPseudo('GamerDu13');
        $user->setAdresse('123 rue de la Victoire, 13000 Marseille');
        $user->setCreatedAt(new \DateTime());
        $user->setIsActive(true);

        $manager->persist($user);

        // 👑 2. Création du compte Administrateur
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']); // <--- LE FAMEUX BADGE VIP
        $admin->setPassword($this->hasher->hashPassword($admin, 'azerty'));
        $admin->setPseudo('SuperAdmin');
        $admin->setAdresse('1 Place de la Matrice, 75000 Paris');
        $admin->setCreatedAt(new \DateTime());
        $admin->setIsActive(true);

        $manager->persist($admin);

        // 3. Création de 10 faux Produits
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setNom('PC Gamer Edition Mega ' . $i);
            $product->setPrix(999.99 + ($i * 50));
            $product->setDescription('Machine de guerre numéro ' . $i);
            $product->setStockQuantite(15);
            $product->setIsActive(true);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
