<?php
namespace App\Command\Util;


use App\Entity\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\WosIdentificator;
use App\Entity\UserArticle;
use App\Form\ArticleType;

class ArticleHelper
{



    public static function calculateAuthorsEffNumber( $authorsNo)
    {
        if($authorsNo<=5){
            $effectiveAuthorsNumber=$authorsNo;
        }elseif($authorsNo <= 80){
            $effectiveAuthorsNumber=(float)(($authorsNo+10)/3.0);
        }else{
            $effectiveAuthorsNumber=30;
        }

        return $effectiveAuthorsNumber;
    }

    public static function createUserArticleObject($em, Article $article,$identifierType):void
    {

         if($article->getIsUpdatedByAdmin()){
             return;
         }
        $authorsArray=explode(';',trim($article->getAuthors()));

         //reset article theta function.
        $article->setThetaFunction(false);
        $i=0;
        foreach($authorsArray as $author){

            $wosIdentificator=$em->getRepository(WosIdentificator::class)->findOneBy([
                'type'=>$identifierType,
                'identificator'=>trim($author)
            ]);

            if($wosIdentificator!==null){
                $user=$wosIdentificator->getUser();

                //verify if UserArticle already exist
                $userArticle=$em->getRepository(UserArticle::class)->findOneBy([
                   'user'=>$user,
                   'article'=>$article
                ]);

                if($userArticle===null) {

                    $userArticle = new UserArticle();
                    $userArticle->setUser($user);
                    $userArticle->setArticle($article);
                }

                    //set isPrimeAuthor
                    if($i===0){
                        $userArticle->setIsPrimeAuthor(true);
                    }else{
                        $userArticle->setIsPrimeAuthor(false);
                    }


                    if(self::isCorrespondentAuthor(trim($author),$article->getCorespondingAuthors())){
                        $userArticle->setIsCorrespondingAuthor(true);
                        $article->setThetaFunction(true);

                    }else{
                        $userArticle->setIsCorrespondingAuthor(false);
                    }


                    $em->persist($userArticle);

                }

            $i += 1;
        }
        $em->persist($article);
    }

    public static function isCorrespondentAuthor($wosIdentificator,$correspondingAuthors)
    {
        return (strpos($correspondingAuthors,$wosIdentificator)!==false)?true:false;
    }

    public static function getTheNumberOfCorrespondingAuthors(string $reprintAuthorsString)
    {
        $reprintAuthorsArray=explode(';',$reprintAuthorsString);

        $authors=[];
        foreach($reprintAuthorsArray as $rp){
            if(($pos=strpos($rp,'(reprint author)'))!==FALSE){
                $rpName=substr($rp,0,$pos);
            }else{
                $rpName=$rp;
            }
            $authors[]=trim($rpName);
        }
       return count(array_unique($authors));
    }
}