<?php
namespace App\Command;

use App\Service\CsvImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Csv\Reader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvImportCommand extends Command
{

   protected static $defaultName="app:csv-import";
    /**
     * @var CsvImporter
     */
    private $importer;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(CsvImporter $importer,ParameterBagInterface $params)
   {
       parent::__construct();
       $this->importer = $importer;
       $this->params = $params;

       //Ex journal import: php bin/console app:csv-import journal journal2016 2016
   }
   protected function configure()
   {
       $this
           // the short description shown while running "php bin/console list"
           ->setDescription('Import a new CSV')
           // the full command description shown when running the command with
           // the "--help" option
           ->setHelp('This command allows you to import CSV data...')
           ->addArgument('entity_type',InputArgument::REQUIRED,'Entity Type')
           ->addArgument('file_name',InputArgument::REQUIRED,'File name')
           ->addArgument('year',InputArgument::OPTIONAL,'Year')
       ;

   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
       $entityType=$input->getArgument('entity_type');
       $fileName=$input->getArgument('file_name');
       $year=$input->getArgument('year');



       $io=new SymfonyStyle($input,$output);
       $io->title( 'Run CSV importer');

       $csv = Reader::createFromPath($this->params->get('kernel.project_dir').'/src/Data/'.$fileName.'.csv');
       $csv->setHeaderOffset(0);

       $header = $csv->getHeader(); //returns the CSV header record
       $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

       $io->progressStart(iterator_count($records));


       $this->importer->importCSV($entityType,$records,$io, $year);


       $io->progressFinish();
       $io->success('Command exited cleanly!');





      // $result=$this->importer->import($file_name,$entity_type);

      // $output->writeln("The importer response is: ".$result);

   }


}