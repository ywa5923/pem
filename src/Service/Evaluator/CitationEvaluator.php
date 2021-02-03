<?php
namespace App\Service\Evaluator;

use App\Entity\Citation;
use Doctrine\ORM\EntityManagerInterface;

class CitationEvaluator implements EvaluatorInterface
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
        $citation=$this->entityManager->getRepository(Citation::class)
            ->getCitations($userId,$year);

        return[
          'citations'=>($citation)?$citation->getWosCitations():0,
           'totalPoints'=>($citation)?$citation->getWosCitations()/20:0
        ];
    }
}