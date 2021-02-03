<?php
namespace App\Command;


use App\Service\PDF\PDFService;
use Symfony\Component\Console\Command\Command;
use App\Entity\User;
use App\Service\Evaluator\ActivityEvaluator;
use App\Service\Evaluator\ArticleEvaluator;
use App\Service\Evaluator\BookEvaluator;
use App\Service\Evaluator\CitationEvaluator;
use App\Service\Evaluator\NormalizationFactorEvaluator;
use App\Service\Evaluator\PatentEvaluator;
use App\Service\Evaluator\ProjectEvaluator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Settings;


class EmailSenderCommand extends Command
{




    protected static $defaultName="app:sent-emails";
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
    private $twig;
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ArticleEvaluator $articleEvaluator,
        BookEvaluator $bookEvaluator,
        PatentEvaluator $patentEvaluator,
        ActivityEvaluator $activityEvaluator,
        ProjectEvaluator $projectEvaluator,
        CitationEvaluator $citationEvaluator,
        NormalizationFactorEvaluator $normalizationFactorEvaluator,
        ContainerInterface $container,
        PDFService $PDFService,
        \Swift_Mailer $mailer
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
        $this->container=$container;
        $this->PDFService=$PDFService;
        $this->twig=$container->get('templating');
        $this->mailer=$mailer;
    }
    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Send bulk emails')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to send bulk emails to scientist with attached pdf')
            // ->addArgument('entity_type',InputArgument::REQUIRED,'Entity Type')

            ->addArgument('year',InputArgument::REQUIRED,'Year')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $entityType=$input->getArgument('entity_type');
        $io = new SymfonyStyle($input, $output);
        $io->title('Run CSV exporter');
        $year = $input->getArgument('year');

        $users = $this->entityManager->getRepository(User::class)
            ->getScientists();

        $io->progressStart(count( $users));
        foreach ($users as $user) {
            $this->sendEmail($user,$year,$io);
            $io->progressAdvance();
        }
        $io->progressFinish();

    }

    public function getEvaluation(int $userId,int $year):array
    {

        return [
            'articles'=>$this->articleEvaluator->getEvaluation($userId, $year),
            'books'=>$this->bookEvaluator->getEvaluation($userId,$year),
            'patents'=>$this->patentEvaluator->getEvaluation($userId,$year),
            'projects'=>$this->projectEvaluator->getEvaluation($userId,$year),
            'activities'=>$this->activityEvaluator->getEvaluation($userId,$year),
            'citations'=>$this->citationEvaluator->getEvaluation($userId,$year),
            'factor'=>$this->normalizationFactorEvaluator->getEvaluation($userId,$year),
            'year'=>$year
        ];
    }

    public function sendEmail(User $user,$year,$io)
    {
        $userId = $user->getId();
        $html = $this->twig->render('evaluation/pdf/pdf_template.hml.twig', [
            'user_full_name' => $user,
            'evaluation'=>$this->getEvaluation($user->getId(),$year)
        ]);

        $imgPath=sprintf("%s/screenshots/%s.png",
            $this->container->getParameter('kernel.project_dir'),
            $user->getEmail()
        );
        //$imgPath=$this->getParameter('kernel.project_dir')."/screenshots/'.$user->getEmail().'.png';
        $pdf=$this->PDFService->getPDF($html,$imgPath);

        $message = (new \Swift_Message('Evaluare profesionala'))
            ->setFrom('ion.ivan@infim.ro')
            ->setTo($user->getEmail());

        $message->setBody( $this->twig('evaluation/email/template.html.twig',[]));

        $message->attach( new \Swift_Attachment($pdf, 'evaluare_profesionala.pdf', 'application/pdf'));


        $status=$this->mailer->send($message);
        if($status===1){
            $this->addEmailToken($user);
        }else{

            $io->writeln('A problem was encountered when sending email to: '.$user->getEmail());
        }

    }

    public function addEmailToken($user)
    {
        $token=$this->entityManager->getRepository(Settings::class)->findOneBy([
            'name'=>'EMAIL_TOKEN'
        ]);

        if($token){
            $user->setEmailToken($token->getValue());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}