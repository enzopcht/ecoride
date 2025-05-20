<?php

namespace App\DataFixtures;

use App\Entity\CreditTransaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 10 passagers aléatoires avec un mot de passe commun
        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $firstName = iconv('UTF-8', 'ASCII//TRANSLIT', $firstName);
            $lastName = iconv('UTF-8', 'ASCII//TRANSLIT', $lastName);

            $email = strtolower($firstName . '.' . $lastName) . '@mail.com';
            $pseudo = strtolower($firstName) . $faker->numberBetween(10, 99);

            $user = new User();
            $user->setEmail($email);
            $user->setPseudo($pseudo);
            $user->setRoles(['ROLE_PASSENGER']);
            $user->setSuspended(false);
            $user->setCreatedAt(new \DateTimeImmutable());

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            $welcome = new CreditTransaction();
            $welcome->setUser($user);
            $welcome->setAmount(20);
            $welcome->setReason('Welcome Bonus');
            $welcome->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($welcome);

            echo "Ajouté : $email | Pseudo : $pseudo | Mot de passe : password\n";
        }

        // 10 conducteurs aléatoires avec un mot de passe commun
        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $firstName = iconv('UTF-8', 'ASCII//TRANSLIT', $firstName);
            $lastName = iconv('UTF-8', 'ASCII//TRANSLIT', $lastName);

            $email = strtolower($firstName . '.' . $lastName) . '@mail.com';
            $pseudo = strtolower($firstName) . $faker->numberBetween(10, 99);

            $user = new User();
            $user->setEmail($email);
            $user->setPseudo($pseudo);
            $user->setRoles(['ROLE_DRIVER']);
            $user->setSuspended(false);
            $user->setCreatedAt(new \DateTimeImmutable());

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            $welcome = new CreditTransaction();
            $welcome->setUser($user);
            $welcome->setAmount(20);
            $welcome->setReason('Welcome Bonus');
            $welcome->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($welcome);

            echo "Ajouté : $email | Pseudo : $pseudo | Mot de passe : password\n";
        }

        // Employé
        $employee = new User();
        $employee->setEmail('employe@ecoride.fr');
        $employee->setPseudo('employe');
        $employee->setRoles(['ROLE_EMPLOYE']);
        $employee->setSuspended(false);
        $employee->setCreatedAt(new \DateTimeImmutable());
        $employee->setPassword($this->passwordHasher->hashPassword($employee, 'password'));
        $manager->persist($employee);
        echo "Ajouté : employe@ecoride.fr | Pseudo : employe | Mot de passe : password\n";

        // Admin
        $admin = new User();
        $admin->setEmail('admin@ecoride.fr');
        $admin->setPseudo('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setSuspended(false);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        echo "Ajouté : admin@ecoride.fr | Pseudo : admin | Mot de passe : password\n";

        $manager->flush();
    }
}
