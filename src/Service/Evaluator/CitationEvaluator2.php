<?php
namespace App\Service\Evaluator;

use App\Entity\Citation;
use App\Entity\UserArticle;
use App\Entity\WosIdentificator;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;

class CitationEvaluator2 implements EvaluatorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEvaluation(int $userId, int $year):array
    {
        $userArticles=$this->entityManager->getRepository(UserArticle::class)
            ->getLastFiveYearsArticles($userId,$year,ArticleType::SCIENTIFIC_PAPER);

        $results=[];
        $totalCitations=0;
        foreach ($userArticles as $ua){
            $article=$ua->getArticle();
            $citations=$this->getArticleCitationsWithoutAutocitations($userId,$article,$year);
            $citationsNumber=count($citations);
            //if an article have more than 0 citations it is returned to evaluation
            if($citationsNumber>0){
                $report['title']=$article->getTitle();
                $report['authors']=$article->getAuthors();
                $report['journal']=$article->getJournal();
                $report['publicationDate']=$article->getPublicationDate();
                $report['citations']=$citations;
                $results[]=$report;
                $totalCitations+=$citationsNumber;
            }

        }

        return [
            'items'=>$results,
            'totalPoints'=>$totalCitations
        ];
    }

    public function getArticleCitationsWithoutAutocitations($userId,$article,$year)
    {

        $userIdentificators=$this->entityManager->getRepository(WosIdentificator::class)
            ->getIdentifiersByUser($userId,ArticleType::SCIENTIFIC_PAPER);

        $citations=$article->getCitations();
        $goodCitations=[];

        foreach ($citations as $citation){
            $authors=$citation->getAuthors();

           if($citation->getPublicationDate()!=null && $citation->getPublicationDate()->format('Y')==$year){
               continue;
           }
            $excluded=false;
            //if a user's identificator is found in authors list is not included
            foreach ($userIdentificators as $idn){
                $identificator=$idn->getIdentificator();

                if(strpos($authors,$identificator)!==false){
                    $excluded=true;
                    break;
                }
            }
            if(!$excluded){
                $goodCitations[]=$citation;
            }
        }

        return $goodCitations;
    }
}
