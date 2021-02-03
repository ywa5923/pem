<?php
namespace App\Command;

use App\Entity\Article;
use App\Entity\Journal;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateArticlesAIS extends Command
{

    protected static $defaultName="app:update-ais";

    protected $entityManager;

    public function __construct(EntityManagerInterface $em,string $name = null)
    {
        parent::__construct($name);
        $this->entityManager=$em;
    }

    protected function configure()
    {
       $this->setDescription("Update AIS facor for articles")
           ->setHelp("The journal factos are for year-2.")
           ->addArgument('year',InputArgument::REQUIRED, 'Year to update');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io=new SymfonyStyle($input,$output);
        $io->title( 'Update AIS factros');
        $year=$input->getArgument('year');

        $articles=$this->entityManager
            ->getRepository(Article::class)
            ->getYearIntervalArticles($year-3,$year-1,'234',ArticleType::SCIENTIFIC_PAPER);
        $io->progressStart(count($articles));
        $inc=0;
        foreach($articles as $article)
        {
            $ais=$this->getJournalAIS(trim($article->getJournal()),$year-2);
            $article->setAIS($ais);
            $io->progressAdvance();
            $io->writeln($ais."-".$article->getJournal());
            $inc++;
            if($inc%100==0){
               $this->entityManager->flush();
            }
        }
       $this->entityManager->flush();
        $io->progressFinish();
    }
    public function getJournalAIS($journal,$year)
    {
        /** @var Journal $journal */
        $journal= $this->entityManager
            ->getRepository(Journal::class)
            ->getJournalByYear($journal,$year);

        if($journal!==null){
            return $journal->getJournalFactors()[0]->getAIS();
        }else{
            return 0;
        }

    }

}
