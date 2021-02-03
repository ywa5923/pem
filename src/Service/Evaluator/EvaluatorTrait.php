<?php
namespace App\Service\Evaluator;

trait EvaluatorTrait
{
    public function calculateAuthorsEffNumber( $authorsString)
    {

        $separator=strpos($authorsString,',')?',':';';
        $authorsNo=substr_count($authorsString,$separator)+1;
        if($authorsNo<=5){
            $effectiveAuthorsNumber=$authorsNo;
        }elseif($authorsNo <= 80){
            $effectiveAuthorsNumber=(float)(($authorsNo+10)/3.0);
        }else{
            $effectiveAuthorsNumber=30;
        }

        return $effectiveAuthorsNumber;
    }
}