<?php
namespace App\Command\Util;



use App\Entity\Article;
use App\Entity\Journal;
use App\Entity\User;
use App\Entity\UserArticle;
use App\Entity\WosIdentificator;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArticleImporter implements ImporterInterface
{

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var string
     */
    private $articleType;

    public function __construct(EntityManagerInterface $em,SymfonyStyle $io,string $articleType)
    {

        $this->em = $em;
        $this->io = $io;
        $this->articleType = $articleType;
    }

    public function import(\Iterator $records):void
    {
        foreach ($records as $record){

            if( !preg_match('/\d{4}/',$record['PY'])){
                $this->io->writeln('not match published date: '. $record['TI']);
                continue;

            }
            //if there is a book chapter in article's list, skip it
            if($this->articleType==='article' && strpos($record['DT'],'Book Chapter')!==false){
                continue;
            }

            if($this->getArticleByTitle(trim($record['TI']))!==null){
               continue;
            }

            //if the article is of book type, get only book's chapters
            if($this->articleType==='book' && strpos($record['DT'],'Article')===false){
                continue;
            }


            switch($this->articleType){
                case 'article':
                    $article=$this->getArticleObject($record);
                    break;
                case 'book':
                    $article=$this->getBookObject($record);
                    break;
                default:
                    throw new \DomainException('Not matching argument');

            }

           // $this->io->writeln($article->getTitle());
            $this->em->persist( $article);

            $this->em->flush();
            sleep(1);

           ArticleHelper::createUserArticleObject($this->em,$article,ArticleType::SCIENTIFIC_PAPER);
           $this->io->progressAdvance();
        }

        $this->em->flush();
    }

    public function getArticleObject(array $record):Article
    {
        $article=new Article();
        $article->setTitle(trim($record['TI']));
        $article->setAbstract(trim($record['AB']));
        $article->setPublicationDate(\DateTime::createFromFormat('Y',trim($record['PY'])));
        $article->setMiscellanous($record['DT']);
        $article->setCorespondingAuthors(trim($record['RP']));
        $article->setEmailsOfCorespondingAuthors(trim($record['EM']));
        $article->setType(ArticleType::SCIENTIFIC_PAPER);
        $article->setAuthors(trim($record['AU']));
        $article->setJournal(trim($record['SO']));
        $article->setVolume(trim($record['VL']));
        $article->setDoi(trim($record['DI']));
        $ar=trim($record['AR']);
        if(!empty($ar)){
            $beginPage=$record['BP'];
            $endPage=$record['EP'];
            $article->setPages($beginPage.'-'.$endPage);
            $totalPages=(int)$endPage-(int)$beginPage;
            $article->setTotalPages($totalPages);
        }else{
            $article->setPages($ar);
        }

        //get authors effective number
        $neff=ArticleHelper::calculateAuthorsEffNumber(substr_count($record['AU'],';')+1);
        $article->setEffectiveAuthorsNumber($neff);
        $article->setAIS($this->getJournalAIS(trim($record['SO']),2019));
        $nca=ArticleHelper::getTheNumberOfCorrespondingAuthors($record['RP']);
        $article->setTheNumberOfCorrespondingAuthors($nca);

        return $article;

    }


    public function getJournalAIS($journal,$year)
    {
        /** @var Journal $journal */
        $journal= $this->em
            ->getRepository(Journal::class)
            ->getJournalByYear($journal,$year);

        if($journal!==null){
            return $journal->getJournalFactors()[0]->getAIS();
        }else{
            return 0;
        }

    }

    public function getThetaFunctionValue(array $authorEmails)
    {
        $isFromNIMP=0;
        foreach ($authorEmails as $email){
          $user=$this->em->getRepository(User::class)
                     ->findOneBy([
                         'email'=>trim($email)
                     ]);
                    //->findUserByEmailOrCorrespondentAuthorEmail(trim($email));

           if($user!==null){
               $isFromNIMP=1;
               break;
           }
        }

        return $isFromNIMP;
    }

    public function getArticleByTitle(string $title)
    {
        return $this->em->getRepository(Article::class)->findOneBy([ 'title'=>$title]);
    }

    public function createUserAticleObject(string $authors, Article $article):void
    {
        $authorsArray=explode(';',trim($authors));

        $i=0;
        foreach($authorsArray as $author){

            $wosIdentificator=$this->em->getRepository(WosIdentificator::class)->findOneBy([
                'type'=>ArticleType::SCIENTIFIC_PAPER,
                'identificator'=>trim($author)
            ]);

            if($wosIdentificator!==null){
                $user=$wosIdentificator->getUser();
                $userArticle=new UserArticle();
                $userArticle->setUser($user);

                //set isPrimeAuthor
                if($i===0){
                    $userArticle->setIsPrimeAuthor(true);
                }

                if($this->isCorrespondentAuthor($user,$article->getEmailsOfCorespondingAuthors())){
                    $userArticle->setIsCorrespondingAuthor(true);
                }

                $userArticle->setArticle($article);


                $userArticle->setArticle($article);
                $this->em->persist($userArticle);
            }

            $i += 1;
        }
    }

    public function getBookObject($record)
    {

        $article=new Article();
        $article->setTitle(trim($record['TI']));

        $article->setPublicationDate(\DateTime::createFromFormat('Y',trim($record['PY'])));
        $article->setType(ArticleType::BOOK);
        $article->setAuthors($record['AU']);

        $bookTitle=$record['SO'];
        $volume=$record['VL'];
        $editor=$record['BE'];
        $article->setJournal(sprintf("%s vol %s, editor %s",$bookTitle,$volume,$editor));

        $publisher=$record['PU'];
        $publisherAddress=$record['PA'];
        $year=$record['PY'];
        $article->setMiscellanous(sprintf("%s/%s/%s",$publisher,$publisherAddress,$year));

        $beginPage=$record['BP'];
        $endPage=$record['EP'];
        $article->setPages($beginPage.'-'.$endPage);


        $pages=(int)$endPage-(int)$beginPage;
        $article->setTotalPages($pages);


        $article->setDoi($record['BN']);
        //get authors effective number
        $neff=ArticleHelper::calculateAuthorsEffNumber(substr_count($record['AU'],';')+1);
        $article->setEffectiveAuthorsNumber($neff);

        return $article;

    }

    public function isCorrespondentAuthor(User $user,$emailString)
    {
        $email=$user->getEmail();
        $cEmail=$user->getCorespondentAuthorEmail();

        return (($email && strpos($emailString,$email)!==false)
            ||($cEmail && strpos($emailString,$cEmail)!==false));
    }

}