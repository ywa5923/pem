<?php

namespace App\Service\Evaluator;
use App\Entity\Budget;
use App\Entity\UserProject;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;

class ProjectEvaluator implements EvaluatorInterface
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
        $userProjects=$this->entityManager->getRepository(UserProject::class)
        ->getLastThreeYearsProjects($userId,$year);

        $results=[];
        $totalPoints=0;
        foreach ($userProjects as $up)
        {
            $totalNonProfit=0;
            $totalProfit=0;
            $totalBudgetWithCorrection=0;
            $project=$up->getProject();
            $report['title']=$project->getTitle();
            $report['contract']=$project->getContract();

            $projectType=$project->getType();
            $report['projectType']=$projectType;

            $correction=($projectType===ProjectType::INTERNATIONAL)?5:1;

            $report['userType']=$up->getType();
            $report['budgets']=$up->getBudgets();

            foreach($up->getBudgets() as $budget) {
                $budgetType = $budget->getType();
                if (ProjectType::ECONOMIC === $projectType) {
                    $correction = (Budget::PROFIT === $budgetType) ?20: 2;
                }
               // $report['correction'] = $correction;

                $budget = $budget->getBudget();
                if($budgetType==='profit'){
                    $totalProfit+=$budget;
                }else{
                    $totalNonProfit += $budget;
                }

                $totalBudgetWithCorrection += $correction * $budget;
            }
            $report['totalNonProfit']=$totalNonProfit;
            $report['totalProfit']=$totalProfit;
            $report['totalBudgetWithCorrection']=$totalBudgetWithCorrection;

            //get total points for this project
            $total=2*($totalBudgetWithCorrection/1000000);
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