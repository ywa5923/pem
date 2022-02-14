<?php
namespace App\Service\PDF;

use App\Service\PDF\Util\MyPDF;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PDFService
{


    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getPDF($html,$path='',$pdfName='',$mode='S')
    {

        $logo= $this->parameterBag->get('kernel.project_dir').'/src/Service/PDF/Util/logo.png';


        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
       // $pdf->setLogo($logo);
      //  $pdf->setHeaderText("Evaluare profesionala NIMP 2019");


       // $pdf->SetCreator(PDF_CREATOR);
       // $pdf->SetAuthor('Nicola Asuni');
        //$pdf->SetTitle('Evaluare profesionala');
        //$pdf->SetSubject('TCPDF Tutorial');
        //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


        $pdf->SetMargins(1, 25, 1);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


        $pdf->setFontSubsetting(true);


        $pdf->SetFont('dejavusans', '', 25, '', true);

        $pdf->AddPage();

        $pdf->Image($logo, 10, 4, 74, 18, 'PNG');
        $pdf->SetXY(20, 32);
        $pdf->Cell(0, 0, "Evaluare profesionala 2022", 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->SetXY(20, 23);
        $pdf->writeHTML("<hr style='color:red'>", true, false, false, false, '');

        $pdf->SetFont('dejavusans', '', 7, '', true);
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->AddPage();
       // $pdf->Image($img, 7, 22, 200, 150, 'PNG');

        return  $pdf->Output($path.DIRECTORY_SEPARATOR.$pdfName, $mode);

    }
}