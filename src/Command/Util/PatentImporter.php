<?php
namespace App\Command\Util;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\WosIdentificator;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\UserArticle;

class PatentImporter implements ImporterInterface
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(EntityManagerInterface $em,SymfonyStyle $io)
    {

        $this->em = $em;
        $this->io = $io;
    }

    public function import(\Iterator $records):void
    {
        //PN patent number
        //TI title
        //AD Aplication detail
        //AU inventors(authors)
        //AE asignee (INST NAT CERCETARE DEZV FIZICA MATERIALE)
        //PD patent detail
        foreach ($records as $row) {

           $patentNumber=$row['PN'];
            $title=$row['TI'];
            $appDetails=$row['AD'];
            $patentDetail=$row['PD'];
            $inventors=explode(';',$row['AU']);
            //print_r($inventors);
            $assignee=$row['AE'];

            $appDetailArray=explode(';',$appDetails);
            $patentDetailArray=explode(';',$patentDetail);

            //get the last application detail string in array
            $lastAppDetailString=trim($appDetailArray[count($appDetailArray)-1]);
            $lastAppDetailArray=preg_split('/[\s]{2,}/',$lastAppDetailString);

            //get the last patent detail string in array
            $lastPatentDetailString=trim($patentDetailArray[count($patentDetailArray)-1]);
            $lastPatentDetailArray=preg_split('/[\s]{2,}/',$lastPatentDetailString);
            $publicationDate=\DateTime::createFromFormat('j M Y',trim($lastPatentDetailArray[1]));

            $doi=implode('*',$lastAppDetailArray);

            $article=$this->em->getRepository(Article::class)->findOneBy([
                'doi'=>$doi
            ]);

            if($article){
                continue;
            }

            //calculate neff
            $effectiveAuthorsNumber=ArticleHelper::calculateAuthorsEffNumber(count($inventors));

            $patent=new Article();
            $patent->setType(ArticleType::PATENT);
            $patent->setTitle($title);
            $patent->setJournal('Derwent Registry');
            $patent->setPublicationDate($publicationDate);
            $patent->setEffectiveAuthorsNumber($effectiveAuthorsNumber);
            $patent->setDoi($doi);
            $patent->setAuthors(trim($row['AU']));

            $patent->setMiscellanous($lastPatentDetailArray[0].'*'.$lastPatentDetailArray[2].'*'.$lastPatentDetailArray[1]);
            $numbers=explode(';',$patentNumber);

            $lastNumber=$numbers[count($numbers)-1];
            list($no,$type)=explode('-',$lastNumber);

            switch($type[0]){
                case 'A':
                    $patent->setPType(ArticleType::BREVET_NATIONAL_DEPUS);
                    break;
                case 'B':
                    $patent->setPType(ArticleType::BREVET_NATIONAL_ACORDAT);
                    break;
                default:
                    throw new \DomainException('no matching patent type');

            }

            $this->em->persist($patent);
            $this->em->flush();

            sleep(1);


            //Get authors identifiers and attach the users

            ArticleHelper::createUserArticleObject($this->em,$patent,ArticleType::PATENT);

            $this->io->progressAdvance();
        }

        $this->em->flush();
    }

}