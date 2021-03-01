<?php
namespace App\Command;


use App\Entity\User;
use App\Entity\UserArticle;
use App\Form\ArticleType;
use App\Service\Evaluator\ActivityEvaluator;
use App\Service\Evaluator\ArticleEvaluator;
use App\Service\Evaluator\BookEvaluator;
use App\Service\Evaluator\CitationEvaluator2;
use App\Service\Evaluator\NormalizationFactorEvaluator;
use App\Service\Evaluator\PatentEvaluator;
use App\Service\Evaluator\ProjectEvaluator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportEvaluationcommand extends Command{

    protected static $defaultName="app:evaluation-export";
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ArticleEvaluator
     */
    private $articleEvaluator;
    private $bookEvaluator;
    private $patentEvaluator;
    private $activityEvaluator;
    private $citationEvaluator;
    private $projectEvaluator;
    private $normalizationFactorEvaluator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ArticleEvaluator $articleEvaluator,
        BookEvaluator $bookEvaluator,
        PatentEvaluator $patentEvaluator,
        ActivityEvaluator $activityEvaluator,
        ProjectEvaluator $projectEvaluator,
      CitationEvaluator2 $citationEvaluator,
    NormalizationFactorEvaluator $normalizationFactorEvaluator
    )
    {
        parent::__construct();

        //Ex journal import: php bin/console app:csv-import journal journal2016 2016
        $this->entityManager = $entityManager;
        $this->articleEvaluator = $articleEvaluator;
        $this->bookEvaluator=$bookEvaluator;
        $this->patentEvaluator=$patentEvaluator;
        $this->activityEvaluator=$activityEvaluator;
        $this->projectEvaluator=$projectEvaluator;
        $this->citationEvaluator=$citationEvaluator;
        $this->normalizationFactorEvaluator=$normalizationFactorEvaluator;
    }
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Export evaluation as CSV')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to export evaluation as CSV...')
           // ->addArgument('entity_type',InputArgument::REQUIRED,'Entity Type')

            ->addArgument('year',InputArgument::REQUIRED,'Year')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       // $entityType=$input->getArgument('entity_type');
        $io=new SymfonyStyle($input,$output);
        $io->title( 'Run CSV exporter');
        $year=$input->getArgument('year');

        $users=$this->entityManager->getRepository(User::class)
            ->getScientists();

        $file = fopen("evaluation{$year}_CU_NR_ARTICOLE.csv","w");
        fputcsv($file,[
            "Angajat",
            "Titlu stiintific",
            "Factor normare",
            "Punctaj patente",
            "Punctaj carti",
            "Punctaj articole",
            "Punctaj contracte",
            "Punctaj citari",
            "Punctaj activitati",
            "Punctaj total",
            "Punctaj total normat ",
            "Numar articole"
        ]);
        foreach ($users as $user){
           $userId=$user->getId();

            $articles = $this->articleEvaluator->getEvaluation($userId, $year);
            $books = $this->bookEvaluator->getEvaluation($userId, $year);
            $patents = $this->patentEvaluator->getEvaluation($userId, $year);
            $projects = $this->projectEvaluator->getEvaluation($userId, $year);
            $activities = $this->activityEvaluator->getEvaluation($userId, $year);
            $citations = $this->citationEvaluator->getEvaluation($userId, $year);
            $factor = $this->normalizationFactorEvaluator->getEvaluation($userId, $year);


            $total = $articles['totalPoints'] + $books['totalPoints']
                + $patents['totalPoints'] + $projects['totalPoints']
                +$activities['totalPoints']+$citations['totalPoints']/20;
            $normalizedScore=$total*$factor['value'];



          $title=$this->getScientificTitle($user);

          //count scientific papaers
            $papers=$this->entityManager->getRepository(UserArticle::class)->getLastThreeYearsArticles($user,$year,ArticleType::SCIENTIFIC_PAPER);
          //$articlesNumber=count($user->getUserArticles());

           $io->writeln((string)$user."=>".$title."=>".$normalizedScore."=>".count($papers));
            fputcsv($file,[
                $user,
               $title,
                $factor['value'],
                $patents['totalPoints'],
                $books['totalPoints'],
                $articles['totalPoints'],
                $projects['totalPoints'],
                $citations['totalPoints']/20,
                $activities['totalPoints'],
                $total,
                $normalizedScore,
                count($papers)
            ]);
        }




        $io->progressStart();


        $io->progressFinish();
        $io->success('Command exited cleanly!');

    }



    public  function getScientificTitle(User $user)
    {
        $titles=$user->getScientificTitles();

        foreach ($titles as $title){
            if ($title->getEndWith()===null)
                return $title->getGrade();
        }

        return "not found";
    }
}