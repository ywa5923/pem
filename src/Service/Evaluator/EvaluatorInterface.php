<?php

namespace App\Service\Evaluator;
use App\Entity\User;

interface EvaluatorInterface
{
   public function getEvaluation(int $userId, int $year):array;
}