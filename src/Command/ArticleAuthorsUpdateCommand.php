<?php
namespace App\Command;

use App\Entity\Article;

use App\Form\ArticleType;
use App\Command\Util\ArticleHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArticleAuthorsUpdateCommand extends Command
{

    protected static $defaultName="app:articles-update-authors";

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)

    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Update article authors')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to update articles authors ')
            ->addArgument('article_type',InputArgument::REQUIRED,'Entity Type')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //$article_type=($input->getArgument('article_type')==='patent')?WosIdentificator::PATENT_IDENTIFICATOR;

        $inputArg=$input->getArgument('article_type');


        switch ($input->getArgument('article_type')){

            case 'patent':
                $articleType=ArticleType::PATENT;
                $idnType=ArticleType::PATENT;
                break;
            case 'article':
                $articleType=ArticleType::SCIENTIFIC_PAPER;
                $idnType=ArticleType::SCIENTIFIC_PAPER;
                break;
            case 'book':
                $articleType=ArticleType::BOOK;
                $idnType=ArticleType::SCIENTIFIC_PAPER;
                break;
            default:
                throw new \DomainException('no matching patent type');
        }


        $io=new SymfonyStyle($input,$output);
        $io->title("Update authors");

        //get articles
        $articles=$this->entityManager->getRepository(Article::class)->findBy([
           'type'=>$articleType
        ]);

        $io->progressStart(count($articles));

        foreach($articles as $article){
            $io->progressAdvance();
            ArticleHelper::createUserArticleObject($this->entityManager,$article,$idnType);

        }

         $this->entityManager->flush();

    }

}