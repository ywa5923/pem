<?php
namespace App\Command\WebDriver;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Monolog\Logger;
use Symfony\Component\Console\Style\SymfonyStyle;

class WosScrapper
{

    private $wosUrl;
    private $webDriverHost;
    private $username;
    private $password;
    private $searchInstitutionString;
    private $io;
    private $driver;
    private $isFirstWosConnection=true;


    public function __construct(
        string $webDriverHost,
        string $wosUrl,
        string $username,
        string $password,
        string $searchInstitutionString,
        SymfonyStyle $io
    )
    {
        $this->webDriverHost=$webDriverHost;
        $this->wosUrl=$wosUrl;
        $this->username=$username;
        $this->password=$password;
        $this->searchInstitutionString=$searchInstitutionString;
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
          //  $this->driver->get('https://www.webofknowledge.com/');
            $this->driver->get('http://localhost/wos/index');

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


    public function getUserCitations($authorString,$authorEmail)
    {
        try{
            if( $this->isFirstWosConnection){
                $this->isFirstWosConnection=false;
                $this->selectOrganization();
                $this->selectTimeInterval();
                $this->selectSearchByAuthor();
            }else{
                $this->driver->get('https://www.webofknowledge.com/');
                sleep(5);
            }

            $this->addSearchAuthor($authorString);
            return $this->getAuthorCitationsWithScreenShot($authorEmail);
        }catch (\Exception $ex){
             return $ex->getMessage();
        }
    }


    public function selectOrganization()
    {
        sleep(12);
        $this->driver->findElement(WebdriverBy::cssSelector('td.search-criteria-cell2 span.select2-selection__arrow'))->click();
        sleep(5);

        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('td.search-criteria-cell2 select.j-custom-select-medium'))
        );
        $select->selectByValue('OG');

        sleep(3);
        // $str = trim(str_replace( array("\r\n","\r","\n"), ' ' , $strAddress));
        $this->driver->findElement(WebDriverBy::id('value(input1)'))
            ->sendKeys($this->searchInstitutionString); // fill the search box
    }

    public function selectTimeInterval()
    {
        sleep(5);
        //======================set time===========================
        $this->driver->findElement(WebdriverBy::cssSelector('div#timespan span.select2-selection__arrow'))->click();

        sleep(2);
        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('select.j-custom-select-yeardropdown'))
        );
        $select->selectByValue('CUSTOM');

        //select begin year
        $arrows=$this->driver->findElements(WebdriverBy::cssSelector('div.timespan_custom span.select2-selection__arrow'));
        $arrows[0]->click();
        //startyear
        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('select.startyear'))
        );
        $select->selectByValue('2014');
        sleep(2);
        $arrows[1]->click();
        sleep(2);
        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('select.endyear'))
        );
        $select->selectByValue('2018');
    }

    public function selectSearchByAuthor(){
        sleep(4);
        $this->driver->findElement( WebDriverBy::xpath("//div[@id='addSearchRow1']/a"))->click();
        sleep(2);
        $this->driver->findElement(WebdriverBy::cssSelector('tr#searchrow2 td.search-criteria-cell2 span.select2-selection__arrow'))->click();
        sleep(3);
        $select = new WebDriverSelect(
            $this->driver->findElement(WebdriverBy::cssSelector('tr#searchrow2 td.search-criteria-cell2 select.j-custom-select'))
        );
        $select->selectByValue('AU');
    }

    public function addSearchAuthor($authString)
    {
        sleep(4);
        $element=$this->driver->findElement(WebDriverBy::id('value(input2)'));
        $element->clear();
        $element->sendKeys($authString); // fill the search box
        sleep(10);
        //click search btn
        //$this->driver->findElement(WebdriverBy::cssSelector('span#searchCell2 span.searchButton'))->click();
        $this->driver->findElement(WebdriverBy::cssSelector('span#searchCell2 span.searchButton'))->click();
    }

    public function getAuthorCitationsWithScreenShot($userEmail)
    {
        sleep(20);
        //citation
        $webDriverSelector=WebdriverBy::cssSelector('a.citation-report-summary-link');

        if($this->elementExists($webDriverSelector)){
            $this->driver->findElement($webDriverSelector)->click();
        }else{
            return 0;
        }
        //$this->driver->findElement(WebdriverBy::cssSelector('a.citation-report-summary-link'))->click();
        //span#view_citation_report_image
        sleep (15);
        //=================Get citations===================
        $el=$this->driver->findElement(WebDriverBy::cssSelector('table.citation-report-header tr:nth-child(2) td:nth-child(3) em.last'));
        $citations=$el->getAttribute('innerHTML');
        try{
            $this->driver->takeScreenshot("screenshots/{$userEmail}.png");
        }catch(\Exception $ex)
        {
            $this->io->writeln($ex->getMessage());
        }


        return $citations;
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