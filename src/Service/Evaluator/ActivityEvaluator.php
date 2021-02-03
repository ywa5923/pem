<?php
/**
 * Created by PhpStorm.
 * User: ywa
 * Date: 10.03.2019
 * Time: 22:59
 */

namespace App\Service\Evaluator;
use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

class ActivityEvaluator implements EvaluatorInterface
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
        $activities=$this->entityManager->getRepository(Activity::class)
            ->getLastThreeYearsActivities($userId,$year);

        $results=[];
        $totalPoints=0;
        foreach ($activities as $activity){
            $record['type']=$activity->getType();
            $record['title']=$activity->getTitle();
            $record['year']=$activity->getYear();
            $total=$activity->getPoints();
            $record['total']=$total;
            $results[]=$record;
            $totalPoints+=$total;
        }

        return [
            'items'=>$results,
            'totalPoints'=>$totalPoints
        ];
    }

}