<?php

namespace App\Command\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\Console\Style\SymfonyStyle;

class WosScrapper2
{

    private $webDriverHost;
    private $wosURL;
    private $username;
    private $password;
    private $io;
    private $driver;

    public $citationsFound;


    public function __construct(
        string $webDriverHost,
        string $wosURL,
        string $username,
        string $password,
        SymfonyStyle $io
    )
    {
        $this->webDriverHost=$webDriverHost;
        $this->wosURL=$wosURL;
        $this->username=$username;
        $this->password=$password;
        $this->io=$io;

        $this->driver = RemoteWebDriver::create(
            $this->webDriverHost,
            DesiredCapabilities::chrome(),
            1000
        );
    }

    public function login()
    {
        try {
            $this->driver->get('https://www.webofknowledge.com/');
            //$this->driver->get('http://localhost/wos/index');

            sleep(5);
            $pageTitle = $this->driver->getTitle();
            $this->io->writeln($pageTitle);

            //click arrow to  signin
            $this->driver->findElement(WebDriverBy::id('signin'))->click();
            sleep(5);
            $this->io->writeln('clicked login');
            //click sign In
            $this->driver->findElement(WebdriverBy::className('snowplow-header-signin'))->click();
            sleep(5);

            //enter email addr
            $this->driver->findElement(WebDriverBy::id('email'))
                ->sendKeys('ion.ivan@infim.ro');

            sleep(3);

            //enter password
            $this->driver->findElement(WebDriverBy::id('password'))
                ->sendKeys('Punctulverde12#');
            sleep(3);

            //click remeber me
            $this->driver->findElement(WebDriverBy::id('rememberme'))->click();
            sleep(3);

            //click sign me
            $this->driver->findElement(WebDriverBy::id('signInButton'))->click();
            sleep(3);
            $this->io->writeln($pageTitle);
        } catch (\Exception $ex) {
            $this->io->writeln($ex->getMessage());
        }
    }

    public function selectArticle(array $identificator)
    {

        if(!is_array($identificator) || !array_key_exists('identificator',$identificator) || !array_key_exists('searchString',$identificator)){
            throw new \InvalidArgumentException("Invalid identifier");
        }
      //  $this->driver->get('http://localhost/wos/home');

       /// sleep(5);
       // $this->driver->findElement(WebdriverBy::cssSelector('td.search-criteria-cell2 span.select2-selection__arrow'))->click();
     //   sleep(5);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('td.search-criteria-cell2 span.select2-selection__arrow'))
        )->click();

        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('td.search-criteria-cell2 select.j-custom-select-medium'))
        );

        switch($identificator['identificator'])
        {
            case 'title':
                $selectValue='TI';
                break;
            case 'doi':
                $selectValue='DO';
                break;
            default:
                throw new \InvalidArgumentException("Invalid identifier");
        }
        $select->selectByValue($selectValue);

        sleep(3);
        // $str = trim(str_replace( array("\r\n","\r","\n"), ' ' , $strAddress));

        $this->driver->findElement(WebDriverBy::id('value(input1)'))->clear();
        $this->driver->findElement(WebDriverBy::id('value(input1)'))
            ->sendKeys($identificator['searchString']); // fill the search box
        //click search btn
        sleep(2);
        $this->driver->findElement(WebdriverBy::cssSelector('span#searchCell1 span.searchButton'))->click();


    }

    /**
     *
     */
    public function  clickCitation():void
    {
       // $this->driver->get('http://localhost/wos/article');search-results-data-cite
        sleep(2);
        $aSelector=WebdriverBy::cssSelector('div.search-results-data-cite>a');

        //wait for the presence of Time cited label
       $element = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfElementLocated( WebdriverBy::cssSelector('div.search-results-data-cite'))
        );
       //look for a tag and click it if it exists
        if($element1 =$this->elementExists($aSelector)){
         $this->citationsFound=(int)$element1->getText();
           $element1->click();
        }else{
            $this->citationsFound=0;
        }

    }


    public function excludeCitationsYear($yearToExclude)
    {
        $this->driver->get('http://localhost/wos/citations');

        //$elements=$this->driver->findElements(WebdriverBy::cssSelector('div#PublicationYear_tr div.refine-subitem-list div.refine-subitem'));
        $elements = $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('div#PublicationYear_tr div.refine-subitem-list div.refine-subitem'))
        );

      //  dd($elements->getText());
        $foundYear=false;
        foreach ( $elements as $element) {
           if(strpos($element->getText(),(string)$yearToExclude)!==FALSE){
               $foundYear=true;
           }
        }

        if($foundYear)
        {
            $this->io->writeln('I got it '.$yearToExclude);
            $this->driver->findElement(WebdriverBy::cssSelector('a#PublicationYear'))->click();
        }
    }

    public function clickSearch()
    {
        //breadCrumbs
        sleep(2);
        $this->driver->findElement(WebdriverBy::cssSelector('ul.breadCrumbs>li>a.snowplow-searchback'))->click();
    }

    public function  excludeArticleTypes($excludeTypes)
    {

        $elements = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('div#DocumentType_tr div.refine-subitem-list div.refine-subitem'))
        );
        $found=false;
        foreach ( $elements as $element) {
            foreach($excludeTypes as $exclude){
                if(strpos(trim($element->getText()),$exclude)!==FALSE) {
                    $found=true;
                    break;
                }
            }

        }

        $documentTypeSelector=WebdriverBy::cssSelector('a#DocumentType');
        if($found && $element=$this->elementExists($documentTypeSelector)){
            $this->driver->executeScript("arguments[0].scrollIntoView();",[$element]);
            $element->click();
            sleep(2);
            $this->doArticlesRefine($excludeTypes);
            sleep(2);
            return true;


        }elseif ($found){
            //the article's list contains only forbidden types and should not be taken into consideration
            return false;


        }else{
            return true;
        }

    }

    public function testExcludeTypes($excludeTypes){
       
       
        $this->driver->get('http://localhost/wos/exclude_types');
        sleep(3);

        //get all elements tr#DocumentType_raMore_tr>td>table>tbody td.refineItem
        $elements = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector("tr#DocumentType_raMore_tr>td>table>tbody td.refineItem"))
        );
       // dump($elements2);
        $element=$this->driver->findElement(WebdriverBy::cssSelector("#DocumentType_raMore_tr > td > table > tbody > tr > td:nth-child(5)"));
       $c = $element->findElement(WebdriverBy::xpath("preceding-sibling::td[1]/input[@type='checkbox']"));
        $c->click();
        foreach ( $elements as $element) {
            $element->click();
            foreach($excludeTypes as $exclude){
                if(strpos($element->getText(),$exclude)!==FALSE){
                    dump("Exclude: ".$exclude,"-->".$element->getText());
                    sleep(3);
                   $element->click();
                   
                   break;
                }

            }

        }

        sleep(2);
        //get elements and click Refine
        $buttons= $this->driver->findElements(WebdriverBy::cssSelector('div.more_title'));
        foreach ($buttons as $button)
        {
            if(strpos($button->getText(),'Exclude')!==FALSE){
                $button->click();
                break;
            }
        }

    }

    public function doArticlesRefine($excludeTypes)
    {
        //$this->driver->get('http://localhost/wos/refine-document-type');
        sleep(3);

        //get all elements tr#DocumentType_raMore_tr>td>table>tbody td.refineItem
        $elements = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector("tr#DocumentType_raMore_tr>td>table>tbody td.refineItem"))
        );
       // $elements=$this->driver->findElements(WebdriverBy::cssSelector("tr#DocumentType_raMore_tr>td>table>tbody td.refineItem"));
        foreach ( $elements as $element) {
            
            foreach($excludeTypes as $exclude){
                if(strpos($element->getText(),$exclude)!==FALSE){
                    $checkbox = $element->findElement(WebdriverBy::xpath("preceding-sibling::td[1]/input[@type='checkbox']"));
                    $checkbox->click();
                   break;
                }

            }

        }

        sleep(2);
        //get elements and click Refine
        $buttons= $this->driver->findElements(WebdriverBy::cssSelector('div.more_title'));
        foreach ($buttons as $button)
        {
            if(strpos($button->getText(),'Exclude')!==FALSE){
                $button->click();
                break;
            }
        }

    }

    public function getCitations($citationNumber)
    {
        $articles=[];

       // $this->driver->get('http://localhost/wos/citations100');
        $documentTypeSelector=WebdriverBy::cssSelector('span.select2-selection__arrow');
         if($citationNumber>10 && $this->elementExists($documentTypeSelector)) {
             sleep(2);
             $element = $this->driver->wait(3600,1000)->until(
                 WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("span.select2-selection__arrow"))
             );
            // $this->driver->getMouse()->mouseMove( $element->getCoordinates() );
             $this->driver->executeScript("arguments[0].scrollIntoView();",[$element]);
         $element->click();

         //click 50 select#selectPageSize_bottom
             $select = new WebDriverSelect(
                 $this->driver->findElement(WebdriverBy::cssSelector('select#selectPageSize_bottom'))
             );
            $select->selectByValue('50');

      }
        sleep(5);
         //grab articles here
        $a=$this->grabCitations();
        $articles=array_merge($articles,$a);

        //if pagination next is not disabled
        while(!$this->elementExists( WebdriverBy::cssSelector("a.paginationNextDisabled"))){
            sleep(3);
            $paginationNext=WebdriverBy::cssSelector('a.paginationNext');
            if($this->elementExists( $paginationNext)) {
                $element = $this->driver->wait(3600,1000)->until(
                    WebDriverExpectedCondition::presenceOfElementLocated($paginationNext)
                );
                // $this->driver->getMouse()->mouseMove( $element->getCoordinates() );
                $this->driver->executeScript("arguments[0].scrollIntoView();", [$element]);
                sleep(3);
                $element->click();

                sleep(5);
                $a=$this->grabCitations();
                $articles=array_merge($articles,$a);
            }
        }

       /* $inc=1;
        foreach($articles as $article)
        {
            $this->io->writeln($inc.') '.$article['authors']);
           // dd($inc.') '.$article['title']);
            $inc++;
        }*/

       return $articles;

    }

    public function grabCitations():array
    {
        //grab articles here
        $articles=[];
        $elementsText = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector("div.search-results-content"))
        );

        foreach($elementsText as $element)
        {
            $journal=str_replace("\n","",$element->findElement(WebdriverBy::cssSelector('div:nth-child(3)'))->getText());

            //if journal start with Conference then extract date from following div
            if(strpos(trim($journal),"Conference:")===0){
                $journal1=str_replace("\n","",$element->findElement(WebdriverBy::cssSelector('div:nth-child(4)'))->getText());
                $journal.=$journal1;
                $publishedDate=substr(trim($journal1),-4);
            }else{
                $publishedDate=substr(trim($journal),-4);
            }
           $article=[
               'title'=>$element->findElement(WebdriverBy::cssSelector('a.smallV110'))->getText(),
              // 'authors'=>str_replace("By: ",'',$element->findElement(WebdriverBy::cssSelector('div:nth-child(2)'))->getText()),
               'journal'=>  $journal,
               'publishedDate'=>$publishedDate
           ];
          //$this->io->writeln("Data publicarii: ".$journal->findElement(WebdriverBy::cssSelector('span:nth-child(12)'))->getText());
           //open another tab and get authors:
            $url=$element->findElement(WebdriverBy::cssSelector('a.smallV110'))->getAttribute("href");
            $this->driver->executeScript("window.open('about:blank','_blank');", array());
            $this->driver->switchTo()->window($this->driver->getWindowHandles()[1]);
            $this->driver->get($url);
            sleep(2);
            $authors = $this->driver->wait(3600,1000)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("div.block-record-info>p"))
            );

             //click on More if it exists
            $moreText=WebdriverBy::cssSelector('span#show_more_authors_authors_txt_label');
            if($moreBtn=$this->elementExists(  $moreText)) {
                $moreBtn->click();
                sleep(1);
            }
            $article['authors']=preg_replace(['/\[.*?\]/','/\(.*?\)/','/By:/','/\.\.\.Less/'],"",$authors->getText());
            //close tab
            $this->driver->close();
            $articles[]=$article;

            //go to main window
            $this->driver->switchTo()->window($this->driver->getWindowHandles()[0]);
            sleep(2);

        }
        return $articles;
    }

    public function elementsExist(WebDriverBy $webDriverSelector){
        try{
            return $this->driver->findElements($webDriverSelector);

        }
        catch(\Exception $e){
            return false;
        }
    }
    public function elementExists(WebDriverBy $webDriverSelector){
        try{
            return $this->driver->findElement($webDriverSelector);

        }
        catch(\Exception $e){
            return false;
        }
    }

}
