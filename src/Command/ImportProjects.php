<?php
namespace App\Command;

use App\Entity\Budget;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserProject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use League\Csv\Reader;

class ImportProjects extends Command
{

    protected static $defaultName="app:projects-import";
    /**
     * @var CsvImporter
     */
    private $importer;
    /**
     * @var ParameterBagInterface
     */
    private $params;
    /**
     * @var ParameterBagInterface
     */
    private $bag;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ParameterBagInterface $bag,EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->bag = $bag;
        $this->entityManager = $entityManager;

    }
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import projects in new database')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to import projects from csv to database')

        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io=new SymfonyStyle($input,$output);
        $io->writeln("Importing projects...");
        $csv = Reader::createFromPath($this->bag->get('kernel.project_dir').'/src/Data/lista-proiecte2.csv');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

        $io->progressStart(iterator_count($records));

        foreach ($records as $record){
            //$io->writeln($record['nr_contract']);

            if(empty($record['email'])) {
                continue;
            }



            $project=$this->findProject($record['nr_contract']);
            $user=$this->entityManager->getRepository(User::class)->getUsersByEmail($record['email']);

            if(!$user){
                continue;
            }

            if(!$project) {
                $project=$this->mapRowToProjectObject($record);
                $userProject=new UserProject();
                $userProject->setUser($user);
                $userProject->setType(strtolower($record['rol_salariat']));
                $userProject->setProject($project);

            }else{
                $userProject=$this->entityManager->getRepository(UserProject::class)->findOneBy([
                    'user'=>$user,
                    'project'=>$project
                ]);

                if(!$userProject){
                    $userProject=new UserProject();
                    $userProject->setUser($user);
                    $userProject->setProject($project);
                    $userProject->setType(strtolower($record['rol_salariat']));
                }
            }

            $budgets=$this->getBudgets($record);
            foreach($budgets as $budget){
                $userProject->addBudget($budget);
            }
            if(count($budgets)>0){
                $this->entityManager->persist($userProject);
                $this->entityManager->flush();
                $this->entityManager->clear();
            }



          $io->progressAdvance();
        }

        $io->progressFinish();
    }

    public function findProject($contractNr)
    {
        return $this->entityManager->getRepository(Project::class)->findOneBy([
            'contract'=>$contractNr
        ]);
    }

    public function mapRowToProjectObject($record)
    {
            $project=new Project();
            $project->setTitle($record['titlu_proiect']);
            $project->setType(strtolower($record['denumire_proiect']));
            $project->setCategory($record['tip_proiect']) ;
            $project->setContract($record['nr_contract']);
           //$this->entityManager->persist($project);
           // $this->entityManager->flush();

            return $project;
    }

    public function mapBudget($year,$type,$amount){
        $budget=new Budget();
        $budget->setType($type);
        $budget->setBudget($amount);
        $budget->setYear(\DateTime::createFromFormat('Y',$year));
       // $this->entityManager->persist($budget);
       // $this->entityManager->flush();
        return $budget;
    }

    public function getBudgets(array $record){
        $budgets=[];
        if((int)$record['y1']!==0){
            $budgets[]=$this->mapBudget(2017,'nonprofit',(double)$record['y1']);

        }
        if((int)$record['y2']!==0){
            $budgets[]=$this->mapBudget(2016,'nonprofit',(double)$record['y2']);
        }

        if((int)$record['p1']!==0){
            $budgets[]=$this->mapBudget(2017,'profit',(double)$record['p1']);
        }
        if((int)$record['p2']!==0){
            $budgets[]=$this->mapBudget(2016,'profit',(double)$record['p2']);
        }
        return $budgets;
    }


}