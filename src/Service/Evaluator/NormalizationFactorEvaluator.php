<?php
namespace App\Service\Evaluator;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\WorkInterruption;
class NormalizationFactorEvaluator implements EvaluatorInterface
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
       $days=$this->getInterruptionsDaysNumber($userId,$year);

       $normalizationFactor=1098/(1098-$days);

       return [
           'interruptionsDays'=>$days,
            'value'=>$normalizationFactor
       ];
    }

    public function getInterruptionsDaysNumber($userId,$year)
    {
       $wis=$this->entityManager
        ->getRepository(WorkInterruption::class)
        ->findByUser($userId,$year);

        $totalDays=0;

        $timezone = new \DateTimeZone('Europe/Bucharest');
        $startEvaluationDate=\DateTime::createFromFormat('Y-m-d',sprintf("%s-%s-%s",$year-3,1,1));
        $startEvaluationDate->setTimezone($timezone);

        $finishEvaluationDate=\DateTime::createFromFormat('Y-m-d',sprintf("%s-%s-%s",$year-1,12,31));
        $finishEvaluationDate->setTimezone($timezone);

        foreach($wis as $item)
        {
            $beginDate=$item->getBeginDate();
            $beginDate->setTimeZone($timezone);

            $endDate=$item->getEndDate();
            $endDate->setTimeZone($timezone);

            if($endDate>$finishEvaluationDate){
                $endDate=$finishEvaluationDate;
            }

            if($beginDate<$startEvaluationDate){
                $beginDate=$startEvaluationDate;
            }
            $diff=$endDate->diff($beginDate);
            $totalDays+=$diff->format('%a');
        }

        return $totalDays;
    }
}