<?php
namespace App\Command;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearArticlesCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected static $defaultName="app:remove-articles";

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function configure()
    {
        $this->setDescription('Delete all articles with related user_article tables');

        $this->addArgument('entity_type',InputArgument::REQUIRED,'Specify entity type:book,article,patent');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $entityType=$input->getArgument('entity_type');
        if(!in_array($entityType,[ArticleType::SCIENTIFIC_PAPER,ArticleType::PATENT,ArticleType::BOOK])){
            throw new \Exception("entity type not found");
        }
        $io=new SymfonyStyle($input,$output);

        $articles=$this->entityManager->getRepository(Article::class)->findBy([
            'type'=>$entityType
        ]);

        $io->progressStart(count($articles));
        foreach ($articles as $article){

            $userArticles=$article->getArticleAuthors();

            foreach ($userArticles as $item ){
                $this->entityManager->remove($item);
                $this->entityManager->flush();

            }
            $this->entityManager->remove($article);

            $io->progressAdvance();
        }
        $this->entityManager->flush();

        $io->progressFinish();
    }
}