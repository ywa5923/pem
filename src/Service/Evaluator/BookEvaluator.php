<?php

namespace App\Service\Evaluator;

use App\Entity\UserArticle;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class BookEvaluator implements EvaluatorInterface
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
            ->getLastThreeYearsArticles($userId,$year,ArticleType::BOOK);


        //$userArticles=$user->getUserArticles();

        $results=[];
        $totalPoints=0;
        foreach ($userArticles as $ua)
        {
            $article=$ua->getArticle();
            $report['title']=$article->getTitle();
            $report['authors']=$article->getAuthors();

            $report['journal']=$article->getJournal();
            $report['isbn']=$article->getDoi();
            $report['miscellanous']=$article->getMiscellanous();
            $report['pages']=$article->getPages();

            $neff=($articleNeff=$article->getEffectiveAuthorsNumber())?
                $articleNeff:$this->calculateAuthorsEffNumber($article->getAuthors());
            $report['neff']= $neff;

            $totalPages=(int)$article->getTotalPages();
            $total=(0.2*$totalPages)/(10*$neff);
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