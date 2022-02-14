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

        try{
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
       
        sleep(5);




        $settingsRepository=$this->entityManager->getRepository(Settings::class);
        $scrapperToken=$settingsRepository->findOneBy([
            'name'=>'WOS_TOKEN'
        ]);

      //grab all articles
        $articles=$this->entityManager->getRepository(Article::class)->getYearIntervalArticles(2017,2021,$scrapperToken->getValue());

        $io->progressStart(count($articles));

        foreach($articles as $article)
        {
            if(!empty($article->getDoi())){
                $wosScrapper->selectArticle(['searchString'=>$article->getDoi(),'identificator'=>'doi']);
               
            }else{
                $io->writeln($article->getTitle()."=> by title");
                $wosScrapper->selectArticle(['searchString'=>$article->getTitle(),'identificator'=>'title']);
               
               
            }
            sleep(3);
           // $excludeTypes=["CORRECTION"];
           // $wosScrapper->excludeArticleTypes($excludeTypes);
            //de facut
           $wosScrapper->clickCitation();
          
            sleep(6);
            //$excludeTypes=["EDITORIAL MATERIAL","EARLY ACCESS","MEETING ABSTRACT","CORRECTION"];
            $excludeTypes=["Document Types: Meeting Abstract","Document Types: Early Access","Document Types: Editorial Materials","Document Types: Correction"];
            //if none of the exclude type is presented, the scrapper have to select the included type and click Refine in order to have access to citations elements
          
            if($wosScrapper->citationsFound>0 ){
                $wosScrapper->excludeArticleTypes($excludeTypes);
            }  
          
            if( $wosScrapper->allCitationsExcluded){
           //found an article with all types  in the exclude array so continue
           $io->writeln("----Found only citations in excluded types: ".$article->getTitle());
           }

           sleep(2);

            if($wosScrapper->citationsFound>0 ){

                //grab the citations and save them in database
                $citations=$wosScrapper->getCitations();
                foreach($citations as $c){
                    $citation=new ArticleCitation();
                    $citation->setTitle($c['title']);
                    $citation->setAuthors($c['authors']);
                    $citation->setJournal($c['journal']);
                   
                    if($publicationDate=\DateTime::createFromFormat('Y',substr($c['publishedDate'],-4))){
                       
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
            sleep(2);
        }

        $io->progressFinish();

        }catch(\Exception $e)
        {
           $wosScrapper->close();
           $this->execute($input,$output);
            
        }

      
    }

    


}
