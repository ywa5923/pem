<?php
namespace App\Command\Util;

use App\Entity\User;
use App\Entity\UserScientificTitle;
use App\Entity\WosIdentificator;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserImporter implements ImporterInterface
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

    public function import(\Iterator $records)
    {
        foreach ($records as $row) {
           //$this->io->writeln($row['first_name'].'======='.$row['last_name']);

           $user=new User();
           $lastName=ucfirst(strtolower(trim($row['last_name'])));
           $user->setLastName($lastName);
           $user->setEmail(trim($row['email']));
           $user->setSection(trim($row['section']));

            $names= preg_split("/[\s]+/", $row['first_name']);

            $f=strtoupper(substr($names[0],0,1));
            $user->setFirstName(ucfirst(strtolower($names[0])));

            $this->em->persist($user);
            $this->em->flush();

           if(count($names)===2){

               $user->setMiddleName(ucfirst(strtolower($names[1])));
               $m=strtoupper(substr($names[1],0,1));


               $a1=$lastName.', '.$f.$m;
               $a2=$lastName.', '.$m.$f;
               $p1=strtoupper($lastName).' '.$f.' '.$m;
               $p2=strtoupper($lastName).' '.$m.' '.$f;




                   $w1=new WosIdentificator();
                   $w1->setType(ArticleType::SCIENTIFIC_PAPER);
                   $w1->setUser($user);
                   $w1->setIdentificator($a1);
                   $this->em->persist($w1);

                   if($a2!==$a1){
                       $w2=new WosIdentificator();
                       $w2->setType(ArticleType::SCIENTIFIC_PAPER);
                       $w2->setUser($user);
                       $w2->setIdentificator($a2);
                       $this->em->persist($w2);
                   }




               $w3=new WosIdentificator();
               $w3->setType(ArticleType::PATENT);
               $w3->setUser($user);
               $w3->setIdentificator($p1);
               $this->em->persist($w3);

               if($p2!==$p1){
                   $w4=new WosIdentificator();
                   $w4->setType(ArticleType::PATENT);
                   $w4->setUser($user);
                   $w4->setIdentificator($p2);
                   $this->em->persist($w4);
               }

           }else{


               $a1=$lastName.', '.$f;
               $p1=strtoupper($lastName).' '.$f;

               $w3=new WosIdentificator();
               $w3->setType(ArticleType::SCIENTIFIC_PAPER);
               $w3->setUser($user);
               $w3->setIdentificator($a1);
               $this->em->persist($w3);

               $w4=new WosIdentificator();
               $w4->setType(ArticleType::PATENT);
               $w4->setUser($user);
               $w4->setIdentificator($p1);
               $this->em->persist($w4);

           }

           //set scientific grade

           $userScientificTitle=new UserScientificTitle();
           $userScientificTitle->setUser($user);
           $userScientificTitle->setGrade(trim($row['grade']));

           //$user->setScientificTitle(trim($row['grade']));

           $this->em->persist($userScientificTitle);

           $this->io->progressAdvance();

        }

        $this->em->flush();
    }

}
