<?php
/**
 * Created by PhpStorm.
 * User: ywa
 * Date: 10.03.2019
 * Time: 23:00
 */

namespace App\Service\Evaluator;
use App\Entity\UserArticle;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PatentEvaluator implements EvaluatorInterface
{
    use EvaluatorTrait;
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
            ->getLastThreeYearsArticles($userId,$year,ArticleType::PATENT);


        //$userArticles=$user->getUserArticles();

        $results=[];
        $totalPoints=0;
        foreach ($userArticles as $ua)
        {
            $article=$ua->getArticle();
            $report['title']=$article->getTitle();
            $report['authors']=$article->getAuthors();


           // $neff=($articleNeff=$article->getEffectiveAuthorsNumber())?
            //    $articleNeff:$this->calculateAuthorsEffNumber($article->getAuthors());
            $neff=$this->calculateAuthorsEffNumber($article->getAuthors());
            $report['neff']=$neff;

            $report['doi']=$article->getDoi();
            $report['journal']=$article->getJournal();
            $report['miscellanous']=$article->getMiscellanous();

            $patentType=$article->getPtype();

            $report['patentType']=$patentType;

            switch($patentType){
                case ArticleType::BREVET_NATIONAL_ACORDAT:
                    $score=0.5;
                    break;
                case ArticleType::BREVET_NATIONAL_DEPUS:
                    $score=0.1;
                    break;
                case ArticleType::BREVET_NATIONAL_PUBLICAT:
                    $score=0.25;
                    break;
                case ArticleType::BREVET_INTERNATIONAL_DEPUS:
                    $score=0.6;
                    break;
                case ArticleType::BREVET_INTERNATIONAL_ACORDAT:
                    $score=3;
                    break;
                case ArticleType::BREVET_INTERNATIONAL_PUBLICAT:
                    $score=1.5;
                    break;
                default:
                    throw new \DomainException("The patent type could not be resolve");

            }
            $total=$score/$neff;

            $report['total']   =$total;
            $totalPoints+=$total;
            $results[]=$report;

        }

        return [
            'items'=>$results,
            'totalPoints'=>$totalPoints
        ];
    }

}