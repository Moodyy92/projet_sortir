<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

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

        $sortie= new Sortie();
        $sortie->setNom("Mathieu");
        $sortie->setDateHeureDebut((new \DateTime())->add(new \DateInterval("P4D"))); //P pour +, 4 pour nombre, D pour Day
        $sortie->setDuree(new \DateInterval("P1W"));//P pour +, 1 pour nombre, W pour week
        $sortie->setDateLimiteInscription((new \DateTime())->add(new \DateInterval("P1D")));
        $sortie->setNbInscriptionMax(5);
        $sortie->setInfosSortie("Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.");
        $sortie->setEtat($ouverte);
        $sortie->setLieu($lieu);
        $sortie->addParticipant($admin);
        $sortie->setOrganisateur($admin);
        $sortie->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($sortie);
        $manager->flush();
    }
}
