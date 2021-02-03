<?php
namespace App\Service;
use App\Command\Util\PatentImporter;
use App\Command\Util\UserImporter;
use App\Command\Util\JournalImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Command\Util\ArticleImporter;

class CsvImporter
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    public function importCSV(string $entity,\Iterator $records,SymfonyStyle $io,$year=null)
    {

        switch($entity){
            case 'user':
                $importer=new UserImporter($this->em,$io);
                break;
            case 'journal':
                $importer=new JournalImporter($this->em,$io,$year);
                break;
            case 'patent':
                $importer=new PatentImporter($this->em,$io);
                break;
            case 'article':
                $importer=new ArticleImporter($this->em,$io,$entity);
                break;
            case 'book':
                $importer=new ArticleImporter($this->em,$io,$entity);
                break;
            default:
                throw new \DomainException('no matching importer');
        }

      return $importer->import($records);
    }
}