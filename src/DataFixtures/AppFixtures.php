<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $paris = new Ville();
        $paris->setNom("Paris");
        $paris->setCodePostal(75200);
        $manager->persist($paris);
        $manager->flush();

        $lieu = new Lieu();
        $lieu->setNom("Arc de Triomphe");
        $lieu->setRue("Champs Elysées");
        $lieu->setVille($paris);
        $lieu->setLatitude(2.29531);
        $lieu->setLongitude(48.87366);
        $manager->persist($lieu);

        $creee = new Etat();
        $creee->setLibelle("Créée");
        $manager->persist($creee);

        $ouverte = new Etat();
        $ouverte->setLibelle("Ouverte");
        $manager->persist($ouverte);

        $cloturee = new Etat();
        $cloturee->setLibelle("Cloturée");
        $manager->persist($cloturee);

        $activite = new Etat();
        $activite->setLibelle("Activité en cours");
        $manager->persist($activite);

        $passee = new Etat();
        $passee->setLibelle("Passée");
        $manager->persist($passee);

        $annulee = new Etat();
        $annulee->setLibelle("Annulée");
        $manager->persist($annulee);

        $campus = new Campus();
        $campus->setNom("Ecole 42");
        $manager->persist($campus);
        $manager->flush();

        $admin = new Participant();
        $admin->setNom("admin");
        $admin->setPrenom("admin");
        $admin->setTelephone("0654218532");
        $admin->setEmail("admin@hotmail.fr");
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            "password"
        ));
        $admin->setActif(true);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setCampus($campus);
        $admin->addRole('ROLE_ADMIN');
        $manager->persist($admin);

        $manager->flush();
    }
}
