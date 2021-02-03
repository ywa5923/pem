<?php
namespace App\Service\Evaluator;

use App\Entity\Article;
use App\Entity\User;

use App\Entity\UserArticle;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;

class ArticleEvaluator implements EvaluatorInterface
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
            ->getLastThreeYearsArticles($userId,$year,ArticleType::SCIENTIFIC_PAPER);

        $results=[];
        $totalPoints=0;

        foreach ($userArticles as $ua)
        {
            $article=$ua->getArticle();
            $report['title']=$article->getTitle();
            $report['authors']=$article->getAuthors();
            $journal=$article->getJournal();
            $pages=$article->getPages();
            $volume=$article->getVolume();
            $year = ($yearDate=$article->getPublicationDate())?($yearDate->format('Y')):'NULL';
            $doi=$article->getDoi();

            $report['journal']=sprintf('%s vol %s pp. %s %s',$journal,$volume,$pages,$year);
            $report['AIS']=$article->getAis();
            $report['correction']=1+(int)$article->getThetaFunction();
            $i=$this->getIContribution($ua);
            $p=$this->getPContribution($ua);

            $report['i']=$i;
            $report['p']=$p;
            $total=0.5*$i+0.5*$p;
            $report['total']=$total;
            $totalPoints+=$total;

            $neff=($articleNeff=$article->getEffectiveAuthorsNumber())?
                $articleNeff:$this->calculateAuthorsEffNumber($article->getAuthors());

            $report['neff']= $neff;

            $results[]=$report;

        }

        return [
            'items'=>$results,
            'totalPoints'=>$totalPoints
        ];

    }

    public function getIContribution(UserArticle $ua)
    {
        $article=$ua->getArticle();
        $thetaFunction=(int)$article->getThetaFunction();
       // $neff=$article->getEffectiveAuthorsNumber();
        $neff=($articleNeff=$article->getEffectiveAuthorsNumber())?
            $articleNeff:$this->calculateAuthorsEffNumber($article->getAuthors());

        $ais=$article->getAIS();

        return ($ais/$neff)*(1+$thetaFunction);
        
    }

    public function getPContribution(UserArticle $ua){
        $article=$ua->getArticle();
        $isPrimeAuthor=$ua->getIsPrimeAuthor();
        $isCorrespondentAuthor=$ua->getIsCorrespondingAuthor();
        $ais=$article->getAIS();

        $np=$article->getTheNumberOfPrimeAuthors();
        $nc=$article->getTheNumberOfCorrespondingAuthors();


        if($isPrimeAuthor and $isCorrespondentAuthor){
            //$result=(0.75*$ais/$np)+(0.75*$ais/$nc);
            $result=($ais/$np)+($ais/$nc);
        }elseif($isCorrespondentAuthor){
            //$result=(0.75*$ais/$nc);
            $result=($ais/$nc);
        }elseif($isPrimeAuthor && $article->getThetaFunction()){
            //$result=(0.75*$ais/$np);
            $result=($ais/$np);
        }elseif($isPrimeAuthor && !$article->getThetaFunction()){
            $result=(0.5*$ais/$np);
        }else{
            $result=0;
        }

        return $result;
    }
}