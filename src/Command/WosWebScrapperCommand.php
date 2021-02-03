<?php
namespace App\Command;

use App\Command\WebDriver\WosScrapper;
use App\Entity\Settings;
use App\Entity\User;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use App\Entity\Citation;

class WosWebScrapperCommand extends Command
{
    protected static $defaultName="app:wos-get-citations";
    protected $entityManager;

    public function __construct(EntityManagerInterface $em,?string $name = null)
    {
        parent::__construct($name);
        $this->entityManager=$em;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import a new CSV')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to get WOS citations...')
           // ->addArgument('entity_type',InputArgument::REQUIRED,'Entity Type')
           // ->addArgument('file_name',InputArgument::REQUIRED,'File name')
          //  ->addArgument('year',InputArgument::OPTIONAL,'Year')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln("Let do some work");
        $wosUrl = 'https://www.webofknowledge.com';
        $webDriverHost = 'http://localhost:4444/wd/hub';
        $username = 'ion.ivan@infim.ro';
        $password = 'Punctulverde12#';
        //$searchInstitutionString = "(NATIONAL INSTITUTE OF MATERIALS PHYSICS) OR (NATL INST MAT PHYS) OR (NATL INST PHYS MAT) OR (NATL INST MAT) OR (NATL INST MAT PHYSICS) OR (NACL INST MAT PHYS) OR (NATL INST MAT PHYS BUCHAREST) OR (INFM NATL INST MAT PHYS) OR (INST MAT PHYS) OR (INCDFM) OR (NIMP) OR (INST FIZ TEHNOL MAT) OR (NATL INST MAT PHYS INCDFM) OR (NATL INST R D MAT PHYS) OR (R D NATL INST MAT PHYS) OR (NATL INST MAT PHYS BUCHAREST MAGURELE) OR (Natl Inst R&D Mat Phys) OR (Natl Inst Mat Phys, RO-077125 Magurele, Romania) OR (Natl Inst Mat Phys, Atomistilor 405A, Magurele Ilfov 077125, Romania) OR (Natl Inst Mat Phys, 405 A,Atomistilor St, Bucharest 077125, Romania) OR (Inst Natl Pentru Fiz Mat)";
        $searchInstitutionString='Natl Inst Mat Phys';
        //$authorEmail = "ion.ivan@infim.ro";
        //$authString = "I Ivan OR Ivan I";


        $wosScrapper=new WosScrapper(
          $webDriverHost,
          $wosUrl,
          $username,
          $password,
          $searchInstitutionString,
          $io
        );

        $wosScrapper->login();
        sleep(10);



        $settingsRepository=$this->entityManager->getRepository(Settings::class);
        $scrapperToken=$settingsRepository->findOneBy([
            'name'=>'WOS_TOKEN'
        ]);
        $citationYear=$settingsRepository->findOneBy([
            'name'=>'CITATIONS_YEAR'
        ]);

        $users=$this->entityManager
            ->getRepository(User::class)
            ->getScientists();

        foreach($users as $user){
            if($user->getScrapperToken()===$scrapperToken->getValue()){
                continue;
            }
            $email=$user->getEmail();
            $idns=$user->getIdentificators();
            $identifiersArray=[];
            foreach($idns as $idn){
                if($idn->getType()===ArticleType::SCIENTIFIC_PAPER)
                $identifiersArray[]=$idn->getIdentificator();
            }

            $searchStr=implode(' OR ',  $identifiersArray);
            $citations=$wosScrapper->getUserCitations( $searchStr,$email);
            $citation=new Citation();
            $citation->setUser($user);
            $io->writeln($email.' => '.$citations);
            $citation->setWosCitations($citations);
            $citation->setYear(\DateTime::createFromFormat('Y', $citationYear->getValue()));
            $user->setScrapperToken( $scrapperToken->getValue());
            $this->entityManager->persist($citation);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

    }

}
