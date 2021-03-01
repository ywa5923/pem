<?php
namespace App\Command;


use Symfony\Component\Console\Command\Command;


use App\Service\PDF\PDFService;

use App\Entity\User;
use App\Service\Evaluator\ActivityEvaluator;
use App\Service\Evaluator\ArticleEvaluator;
use App\Service\Evaluator\BookEvaluator;
use App\Service\Evaluator\CitationEvaluator2;
use App\Service\Evaluator\NormalizationFactorEvaluator;
use App\Service\Evaluator\PatentEvaluator;
use App\Service\Evaluator\ProjectEvaluator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;



class ExportPDFCommand extends Command
{


    protected static $defaultName="app:export-pdf";

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

    private $PDFService;
    private $container;


    public function __construct(
        EntityManagerInterface $entityManager,
        ArticleEvaluator $articleEvaluator,
        BookEvaluator $bookEvaluator,
        PatentEvaluator $patentEvaluator,
        ActivityEvaluator $activityEvaluator,
        ProjectEvaluator $projectEvaluator,
        CitationEvaluator2 $citationEvaluator,
        NormalizationFactorEvaluator $normalizationFactorEvaluator,
        PDFService $PDFService,
        ContainerInterface $container
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
        $this->PDFService=$PDFService;
        $this->container=$container;

    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Export evaluations as PDF')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you save all evaluations as pdf')
            // ->addArgument('entity_type',InputArgument::REQUIRED,'Entity Type')

            ->addArgument('year',InputArgument::REQUIRED,'Year')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $entityType=$input->getArgument('entity_type');
        $io = new SymfonyStyle($input, $output);
        $io->title('Run PDF exporter');
        $year = $input->getArgument('year');

        $users = $this->entityManager->getRepository(User::class)
            ->getScientists();

        $io->progressStart(count( $users));
        foreach ($users as $user) {
            $this->savePDF($user,$year);
            $io->progressAdvance();
        }
        $io->progressFinish();
    }

    public function savePDF($user,$year){

        $userId = $user->getId();
        $html = $this->container->get('templating')->render('evaluation/pdf/pdf_template.hml.twig', [
            'user_full_name' => $user,
            'evaluation'=>$this->getEvaluation($user->getId(),$year)
        ]);

        $projectDir= $this->container->getParameter('kernel.project_dir');
        $path=sprintf("%s/pdf",
           $projectDir
        );
       
        $pdfName= $user->getEmail().".pdf";
        $this->PDFService->getPDF($html,$path,$pdfName,'F');
      
    }

    public function getEvaluation(int $userId,int $year):array
    {

        return [
            'articles'=>$this->articleEvaluator->getEvaluation($userId, $year),
            'books'=>$this->bookEvaluator->getEvaluation($userId,$year),
            'patents'=>$this->patentEvaluator->getEvaluation($userId,$year),
            'projects'=>$this->projectEvaluator->getEvaluation($userId,$year),
            'activities'=>$this->activityEvaluator->getEvaluation($userId,$year),
            'articlesWithCitations'=>$this->citationEvaluator->getEvaluation($userId,$year),
            'factor'=>$this->normalizationFactorEvaluator->getEvaluation($userId,$year),
            'year'=>$year
        ];
    }



}