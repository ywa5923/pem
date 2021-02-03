<?php

namespace App\Command;


use App\Entity\Article;
use App\Entity\ArticleCitation;
use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Command\WebDriver\WosScrapper2;

class WosWebScrapperCommand2 extends Command
{

    protected static $defaultName = 'app:get-wos-citations2';
    private $entityManager;
    public function __construct(EntityManagerInterface $em,string $name = null)
    {
        parent::__construct($name);
        $this->entityManager=$em;
    }

    protected function configure()
    {
        $this->setDescription("Get wos citations")
            ->setHelp("This command will take the articles from last 5 years and search for citations");
    }

    public function execute(InputInterface $input,OutputInterface $output)
    {
      $io = new SymfonyStyle($input,$output);


        $wosUrl = 'https://www.webofknowledge.com';
        $webDriverHost = 'http://localhost:4444/wd/hub';
        $username = 'ion.ivan@infim.ro';
        $password = 'Punctulverde12#';

        $wosScrapper=new WosScrapper2(
            $webDriverHost,
            $wosUrl,
            $username,
            $password,
            $io
        );

        $wosScrapper->login();

        //$wosScrapper->getCitations(100);
        //exit();
        //$excludeTypes=["EDITORIAL MATERIAL","EARLY ACCESS","MEETING ABSTRACT","CORRECTION"];
        //$wosScrapper->doArticlesRefine( $excludeTypes);
        //exit();
        //$wosScrapper->excludeArticleTypes();
        //exit();
        //$wosScrapper->selectOrganization();
        sleep(5);




        $settingsRepository=$this->entityManager->getRepository(Settings::class);
        $scrapperToken=$settingsRepository->findOneBy([
            'name'=>'WOS_TOKEN'
        ]);

      //grab all articles
        $articles=$this->entityManager->getRepository(Article::class)->getYearIntervalArticles(2015,2019,$scrapperToken->getValue());

        $io->progressStart(count($articles));

        foreach($articles as $article)
        {
            if(!empty($article->getDoi())){
                $wosScrapper->selectArticle(['searchString'=>$article->getDoi(),'identificator'=>'doi']);
            }else{
                $wosScrapper->selectArticle(['searchString'=>$article->getTitle(),'identificator'=>'title']);
            }
            sleep(3);
            $excludeTypes=["CORRECTION"];
            $wosScrapper->excludeArticleTypes($excludeTypes);

            $wosScrapper->clickCitation();
            sleep(3);
            $excludeTypes=["EDITORIAL MATERIAL","EARLY ACCESS","MEETING ABSTRACT","CORRECTION"];
            $remainsOtherCitationTypes=$wosScrapper->excludeArticleTypes($excludeTypes);

            if( $remainsOtherCitationTypes===false){
                //found an article with all types  in the exclude array so continue
                $io->writeln("----Found only citation in excluded types: ".$article->getTitle());
            }
            if($wosScrapper->citationsFound!=0 &&  $remainsOtherCitationTypes){

                sleep(2);

                //grab the citations and save them in database
                $citations=$wosScrapper->getCitations($wosScrapper->citationsFound);
                foreach($citations as $c){
                    $citation=new ArticleCitation();
                    $citation->setTitle($c['title']);
                    $citation->setAuthors($c['authors']);
                    $citation->setJournal($c['journal']);

                    if($publicationDate=\DateTime::createFromFormat('Y',$c['publishedDate'])){
                        $citation->setPublicationDate($publicationDate);
                    }
                    $citation->setArticle($article);
                    $this->entityManager->persist($citation);
                }

            }
            $article->setScrapperToken($scrapperToken->getValue());
            $this->entityManager->flush();

            $io->progressAdvance();

            //get back to search panel
            $wosScrapper->clickSearch();
            sleep(5);
        }

        $io->progressFinish();
    }


}
