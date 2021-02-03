<?php
namespace App\Service\PDF\Util;


class MyPDF extends \TCPDF
{


    private $logoImg;
    private $headerText;
    //Page header
    public function Header() {
        // Logo
        //$image_file = K_PATH_IMAGES.'logo_example.jpg';
       // $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 25);
        // Title
        //$this->Image($this->logoImg, 15, 2, 20, 17, 'PNG');
       // $this->SetXY(25, 11);
       // $this->Cell(0, 0, $this->headerText, 0, false, 'C', 0, '', 0, false, 'M', 'M');
       // $this->SetXY(20, 21);
        //$this->writeHTML("<hr style='color:red'>", true, false, false, false, '');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function setLogo($logo)
    {
        $this->logoImg=$logo;
    }

    public function setHeaderText($text)
    {
        $this->headerText=$text;
    }
}