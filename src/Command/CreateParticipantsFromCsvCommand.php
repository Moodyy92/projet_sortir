<?php

namespace App\Command;


use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


#[AsCommand(
    name: 'CreateParticipantsFromCsv',
    description: 'Import des Participants a partir d\'un csv',
)]
class CreateParticipantsFromCsvCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private $projectDir;


    public function __construct($projectDir,EntityManagerInterface $entityManager)
    {

        parent::__construct();
        $this->projectDir = $projectDir; //permet d'atteindre les classes de partout
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Date Courante
        $aujourdui = new \DateTimeImmutable();

        //Convertit le fichier csv en tableau iterable
        $recupTableau=$this->getCvsRowAsArray();

        $participantRepo= $this->entityManager->getRepository(Participant::class);

        //Recuperation du Repository de Campus
        $campusRepo = $this->entityManager->getRepository(Campus::class);

        //Boucle sur le tableau
        foreach ($recupTableau as $recupTableau)
        {




                $newParticipant = new Participant();
                $newParticipant->setPseudo($recupTableau["pseudo"]);
                $newParticipant->setEmail($recupTableau["email"]);
                $newParticipant->setRoles(array($recupTableau["roles"]));
                $newParticipant->setPassword($recupTableau["password"]);
                $newParticipant->setNom($recupTableau["nom"]);
                $newParticipant->setPrenom($recupTableau["prenom"]);
                $newParticipant->setTelephone($recupTableau["telephone"]);
                $newParticipant->setActif($recupTableau["actif"]);
                $newParticipant->setCreatedAt($aujourdui);
                //Attention au fixture
                $newParticipant->setCampus($campusRepo->find($recupTableau["campus_id"]));

          if($newParticipant != $participantRepo->findOneBy(["id" => $recupTableau["id"]])){
                $this->entityManager->persist($newParticipant);
                $this->entityManager->flush();
            }


        };




        }
        /**************   Convertie et adresse le fichier.csv   ***************/
        public function getCvsRowAsArray()
        {
            //Chemin du fichier
            $inputFile = $this->projectDir.'\public\data\participant .csv';

            //Convertisseur
            $decoder = new Serializer([new ObjectNormalizer()],[new CsvEncoder()]);
            $test=$decoder->decode(file_get_contents($inputFile),'csv');

            //Conversion
            return $decoder->decode(file_get_contents($inputFile),'csv');

        }
}
