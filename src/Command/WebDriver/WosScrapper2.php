<?php

namespace App\Command\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\Console\Style\SymfonyStyle;
use \Facebook\WebDriver\Chrome\ChromeOptions;

class WosScrapper2
{

    private $webDriverHost;
    private $wosURL;
    private $username;
    private $password;
    private $io;
    private $driver;

    public $citationsFound;
    public $allCitationsExcluded;


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

        $desiredCapabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();
        $options->addArguments(array( '--window-size=5500,5500', '--accept-ssl-certs=true' ));
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY,  $options);

        $this->driver = RemoteWebDriver::create(
            $this->webDriverHost,
            //DesiredCapabilities::chrome(),
            $desiredCapabilities,
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
            
           $this->driver->findElement(  WebDriverBy::cssSelector('body > app-wos > div > div > header > app-header > div.container > div.white-bar.ng-star-inserted > div > div.flex-display-align-center.margin-right-15--reversible.ng-star-inserted > button.font-size-16.sign-in.ng-star-inserted > mat-icon > svg'))->click();
            sleep(5);
            $this->io->writeln('clicked login');
            //click sign In
            
          $this->driver->findElement(  WebDriverBy::cssSelector('#mat-menu-panel-3 > div > div > a.mat-focus-indicator.mat-menu-user.mat-menu-item.ng-star-inserted'))->click();
            sleep(5);

            //enter email addr
            $this->driver->findElement(WebDriverBy::id('mat-input-0'))
                ->sendKeys('ion.ivan@infim.ro');

            sleep(3);

            //enter password
            $this->driver->findElement(WebDriverBy::id('mat-input-1'))
                ->sendKeys('Punctulverde12#');
            sleep(3);

            //click remeber me
           // $this->driver->findElement(WebDriverBy::id('rememberme'))->click();
            //sleep(3);

            //click sign me
            $this->driver->findElement(WebDriverBy::id('signIn-btn'))->click();
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
     
        $arrowSelector='#snSearchType > div.row.ng-star-inserted > app-search-row > div > div.selects > app-select-search-field > wos-select > button';
       //'#snSearchType > div.row.ng-star-inserted > app-search-row > div > div.selects > app-select-search-field > wos-select > button > mat-icon > svg'
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector($arrowSelector))
        )->click();
        sleep(2);
        $divOptions=$this->driver->findElement(  WebDriverBy::cssSelector('#global-select > div.options-and-search > div.options'));
       
        //select DOI sau Title from select items
        if($identificator['identificator']==='doi')
        {
            
            $this->driver->executeScript('arguments[0].scrollBy(0,500);', [$divOptions]);

          
            //select DOI
            //#snSearchType > div.row.ng-star-inserted > app-search-row > div > div.search-criteria-input-holder.ng-star-inserted > input
            $this->driver->wait()->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#global-select > div.options-and-search > div.options > div.wrap-mode.ng-star-inserted[aria-label="DOI"]'))
            )->click();
        }else{
            $this->driver->executeScript('arguments[0].scrollBy(0,3);', [$divOptions]);
           //click on title
           
           $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#global-select > div.options-and-search > div.options > div.wrap-mode.ng-star-inserted[aria-label="Title"]'))
        )->click();

        }

        //fill article's doi
        $inputSelector="#snSearchType > div.row.ng-star-inserted > app-search-row > div > div.search-criteria-input-holder.ng-star-inserted > input";
        $this->driver->findElement(WebDriverBy::cssSelector( $inputSelector))->clear();
        $this->driver->findElement(WebDriverBy::cssSelector( $inputSelector))
            ->sendKeys($identificator['searchString']); // fill the search box

        //submit form
        $submitBtnSelector="#snSearchType > div.button-row > button.mat-focus-indicator.cdx-but-md.search.uppercase-button.mat-flat-button.mat-button-base.mat-primary";    
        sleep(2);
        $this->driver->findElement(WebDriverBy::cssSelector( $submitBtnSelector))->click();
        //execute javascript script:https://www.lambdatest.com/blog/executing-javascript-in-selenium-php/

       
    }

    /**
     *
     */
    public function  clickCitation():void
    {
       // $this->driver->get('http://localhost/wos/article');search-results-data-cite
        sleep(2);
        $aSelector=WebdriverBy::cssSelector('body > app-wos > div > div > main > div > div.held > app-input-route > app-base-summary-component > div > div.results.ng-star-inserted > app-records-list > app-record > div.stats-container.ng-star-inserted > div > div.stats-section-section > div.citations.ng-star-inserted a');
        //body > app-wos > div > div > main > div > div.held > app-input-route > app-base-summary-component > div > div.results.ng-star-inserted > app-records-list > app-record > div.stats-container.ng-star-inserted > div > div.stats-section-section > div.citations.ng-star-inserted
       /*$element = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfElementLocated( WebdriverBy::cssSelector('div.search-results-data-cite'))
        );*/

       //look for a tag and click it if it exists
        if($element1 =$this->elementExists( $aSelector)){
         $this->citationsFound=(int)$element1->getText();
           $element1->click();
           $this->io->writeln("citari gasite: ".$this->citationsFound);

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
        $this->driver->findElement(WebdriverBy::cssSelector('#headerLogo'))->click();
    }

    public function refineArticleTypes($includeTypes)
    {
        $documentTypeSelector= $this->driver->findElement(WebdriverBy::cssSelector('#snRefine > div:nth-child(4)'));
        $this->driver->executeScript("arguments[0].scrollIntoView();", [$documentTypeSelector]);
         sleep(2);
        
       
         $elements = $this->driver->wait(3600,1000)->until(
             WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('span.filter-option-name.padding-right-3--reversible'))
         );
        
         foreach ( $elements as $element) {
            // $this->io->writeln($element->getAttribute('title'));
             if(\in_array($element->getAttribute('title'),$includeTypes)){
               
                 $element->click();
                
             }
             
         }
         sleep(2);
         $this->driver->findElement(WebdriverBy::cssSelector('#filter-section-DT > div > div > div > button.mat-focus-indicator.refine-button.uppercase-button.mat-flat-button.mat-button-base.mat-primary.ng-star-inserted'))->click();

    }

    public function  excludeArticleTypes($excludeTypes,$includeTypes)
    {

        //scroll the page;
        //document type div: #snRefine > div:nth-child(4)
        $documentTypeSelector= $this->driver->findElement(WebdriverBy::cssSelector('#snRefine > div:nth-child(4)'));
        $this->driver->executeScript("arguments[0].scrollIntoView();", [$documentTypeSelector]);
        sleep(2);
       
      
        $elements = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('span.filter-option-name.padding-right-3--reversible'))
        );
        $found=0;
        foreach ( $elements as $element) {
           // $this->io->writeln($element->getAttribute('title'));
            if(\in_array($element->getAttribute('title'),$excludeTypes)){
                $found++;
                $element->click();
               
            }
            
        }
        sleep(2);

        //click exclude btn if needed
        //if the number of document's type clicked fileds is equal with the length of excludeTypes array it means 0 real citations

        if($found!=0 && count($excludeTypes)===$found){
            $this->citationsFound=0;
            $this->allCitationsExcluded=true;
        }
        elseif($found!=0 && count($excludeTypes)!==$found){
            $this->driver->findElement(WebdriverBy::cssSelector('#filter-section-DT > div > div > div > button.mat-focus-indicator.refine-button.uppercase-button.mat-stroked-button.mat-button-base.mat-primary.ng-star-inserted > span.mat-button-wrapper'))->click();
        }elseif($found===0){
         
            //click all document types and then refine(this is needed in order to select the citation elements)
            foreach ( $elements as $element) {
                // $this->io->writeln($element->getAttribute('title'));
                 if(\in_array($element->getAttribute('title'),$includeTypes)){
                     $element->click();
                 }
                 
             }
             $this->driver->findElement(WebdriverBy::cssSelector('#filter-section-DT > div > div > div > button.mat-focus-indicator.refine-button.uppercase-button.mat-flat-button.mat-button-base.mat-primary.ng-star-inserted'))->click();

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
        /*$documentTypeSelector=WebdriverBy::cssSelector('span.select2-selection__arrow');
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
        sleep(5);*/

         //grab articles here
        $a=$this->grabCitations2();
        $articles=array_merge($articles,$a);
      
       
       
        
       
      //form.pagination button[aria-label="Bottom Next Page"]
      $nextBtn= $this->driver->findElement(WebdriverBy::cssSelector('form.pagination button[aria-label="Bottom Next Page"]'));
       while($nextBtn->getAttribute("disabled")!=="true"){
            sleep(3);

            $nextBtn->click();
            $a=$this->grabCitations2();
            $articles=array_merge($articles,$a);

            /*$paginationNext=WebdriverBy::cssSelector('a.paginationNext');
            if($this->elementExists( $paginationNext)) {
                $element = $this->driver->wait(3600,1000)->until(
                    WebDriverExpectedCondition::presenceOfElementLocated($paginationNext)
                );
                // $this->driver->getMouse()->mouseMove( $element->getCoordinates() );
                $this->driver->executeScript("arguments[0].scrollIntoView();", [$element]);
                sleep(3);
                $element->click();

                sleep(5);
                $a=$this->grabCitations2();
                $articles=array_merge($articles,$a);
            }*/
        }

     

       return $articles;

    }

    public function grabCitations2():array
    {
        //more btn for author: #SumAuthTa-FrToggle-author-en
        //more article:https://www.webofscience.com/wos/alldb/full-record/WOS:000427505300016

        $articles=[];

        sleep(2);
        /*$titleElements = $this->driver->wait(3600,1000)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector("body > app-wos > div > div > main > div > div.held > app-input-route > app-base-summary-component > div > div.results.ng-star-inserted > app-records-list > app-record:nth-child(7) > div.data-section.ng-star-inserted > div:nth-child(1) > app-summary-title > h3 > a"))
        );*/
        $titlesH3="body > app-wos > div > div > main > div > div.held > app-input-route > app-base-summary-component > div > div.results.ng-star-inserted > app-records-list > app-record:nth-child(7) > div.data-section.ng-star-inserted > div:nth-child(1) > app-summary-title > h3 > a";
        $articleEements=$this->driver->findElements(WebDriverBy::cssSelector("body > app-wos > div > div > main > div > div.held > app-input-route > app-base-summary-component > div > div.results.ng-star-inserted > app-records-list  app-record"));
        
       
        foreach($articleEements as $articleElement){

            $this->driver->executeScript("arguments[0].scrollIntoView();", [$articleElement]);
            sleep(2);

            $this->driver->wait(3600,1000)->until(
                //WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector("div.search-results-content"))
                WebDriverExpectedCondition::visibilityOf($articleElement)
            );
    


            $url="https://www.webofscience.com".$articleElement->findElement(WebDriverBy::cssSelector("a.title"))->getAttribute("href");

            $this->io->writeln("urlul este ".$url);
            $this->io->writeln("articlesElement:  ".count($articleEements));
            $this->driver->executeScript("window.open('about:blank','_blank');", array());
            $this->driver->switchTo()->window($this->driver->getWindowHandles()[1]);
            $this->driver->get($url);

            sleep(2);
           /* $mainArticleBody = $this->driver->wait(3600,1000)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#snMainArticle"))
            );*/
            sleep(2);

             //click on More if it exists
            $moreBtnSelector=WebdriverBy::cssSelector('#SumAuthTa-FrToggle-author-en');
            if($moreBtn=$this->elementExists($moreBtnSelector)) {
                $moreBtn->click();
                sleep(1);
            }
            $titleElement=$this->driver->findElement(WebdriverBy::cssSelector("#FullRTa-fullRecordtitle-0"));
            $authorsElement=$this->driver->findElement(WebdriverBy::cssSelector("#SumAuthTa-MainDiv-author-en"));
            $bublishedElement=$this->driver->findElement(WebdriverBy::cssSelector("#FullRTa-pubdate"));
            
           

            $journalSelector1=WebdriverBy::cssSelector("#snMainArticle > app-jcr-overlay > span > button");
            $journalSelector2=WebdriverBy::cssSelector("#snMainArticle > app-jcr-overlay span.summary-source-title");

            $journalElement=($jrn=$this->elementExists($journalSelector1))?$jrn:($this->driver->findElement( $journalSelector2));

           // $journalElement=$this->driver->findElement(WebdriverBy::cssSelector("#snMainArticle > app-jcr-overlay > span > button"));;
            //  $element=$this->driver->findElement(WebdriverBy::cssSelector("#DocumentType_raMore_tr > td > table > tbody > tr > td:nth-child(5)"));

            
            $article=[
                'title'=>$titleElement->getText(),
               // 'authors'=>str_replace("By: ",'',$element->findElement(WebdriverBy::cssSelector('div:nth-child(2)'))->getText()),
                'journal'=> $journalElement->getText(),
                'publishedDate'=>$bublishedElement->getText(),
                'authors'=>preg_replace(['/\[.*?\]/','/\(.*?\)/','/By:/','/\.\.\.Less/'],"",$authorsElement->getText())
            ];
           
            //close tab
            $this->driver->close();
            $articles[]=$article;

            //go to main window
            $this->driver->switchTo()->window($this->driver->getWindowHandles()[0]);

            dump($article);
            sleep(2);


        }

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
