<?php
namespace App\Command\Util;

use App\Entity\Journal;
use App\Entity\JournalFactor;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Console\Style\SymfonyStyle;

class JournalImporter implements ImporterInterface
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var Integer|null
     */
    private $year;

    /**
     * JournalImporter constructor.
     * @param EntityManagerInterface $em
     * @param \Iterator $records
     * @param SymfonyStyle $io
     * @param Integer|null $year
     */
    public function __construct(EntityManagerInterface $em, SymfonyStyle $io, string $year=null )
    {
        $this->em = $em;
        $this->io = $io;
        $this->year = $year;

    }

    public function import(\Iterator $records)
    {
        foreach ($records as $row) {

            $title=trim($row['title']);
            $abr=$row['abbreviated_title'];


           // $ais=(trim($row['ais'])!=='Not Available')?$row['ais']:0;
           // $jif=(trim($row['jif'])!=='Not Available')?$row['jif']:0;

            $ais=(is_numeric($row['ais']))?$row['ais']:0;
            $jif=(is_numeric($row['jif']))?$row['jif']:0;


            //search for a journal
            $journal=$this->em->getRepository(Journal::class)->findOneBy([
                'name'=>$title
            ]);

            if($journal===null){
                $journal=new Journal();
                $journal->setName($title);
                $journal->setAbbreviatedName($abr);
                $this->em->persist($journal);
                $this->em->flush();
            }
            $journalFactor=new JournalFactor();
            $journalFactor->setJournal($journal);

            $journalFactor->setAIS($ais);
            $year = \DateTime::createFromFormat('Y', $this->year);
            //$y=$year->format('Y-m-d');

            $journalFactor->setYear($year);
            $journalFactor->setImpactFactor($jif);
            $this->em->persist( $journalFactor);
            $this->io->progressAdvance();
        }

        $this->em->flush();
    }
}