<?php

declare(strict_types=1);

/*
 * This file is part of Bilemo
 *
 * (c)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Creation of Products
        for ($i = 0; $i < 20; ++$i) {
            $product = new Product();
            $product->setName('nameProduct'.$i);
            $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, aspernatur.');
            $product->setPrice(rand(100,150) * $i);

            $manager->persist($product);
        }
        // -------------------------------------------------

        // Creation of Clients
        for ($i = 0; $i < 4; ++$i) {
            $client = new Client();
            $client->setUsername('client'.$i);
            $client->setemail('client'.$i.'@gmail.com');
            $client->setPassword($this->userPasswordHasher->hashPassword($client, 'client'.$i));
            $client->setRoles(["ROLE_USER"]);
            $manager->persist($client);

            // Creations of Users
            $nbUsers = rand(4, 9);
            for ($j = 0; $j < $nbUsers; ++$j) {
                $user = new User();
                $user->setFirstname('userFirstname'.$j);
                $user->setLastname('userLastname'.$j);
                $user->setClient($client);
                $manager->persist($user);
            }
        }        

        $manager->flush();
    }
}
