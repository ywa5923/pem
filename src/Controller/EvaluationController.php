<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Entity\User;
use App\Entity\WorkInterruption;
use App\Service\Evaluator\ActivityEvaluator;
use App\Service\Evaluator\ArticleEvaluator;
use App\Service\Evaluator\BookEvaluator;

use App\Service\Evaluator\CitationEvaluator2;
use App\Service\Evaluator\NormalizationFactorEvaluator;
use App\Service\Evaluator\PatentEvaluator;
use App\Service\Evaluator\ProjectEvaluator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\PDF\PDFService;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/evaluation")
 */
class EvaluationController extends AbstractController
{
    /**
     * @var ArticleEvaluator
     */
    private $articleEvaluator;
    /**
     * @var BookEvaluator
     */
    private $bookEvaluator;
    /**
     * @var ActivityEvaluator
     */
    private $activityEvaluator;
    /**
     * @var PatentEvaluator
     */
    private $patentEvaluator;
    /**
     * @var ProjectEvaluator
     */
    private $projectEvaluator;
    /**
     * @var CitationEvaluator
     */
    private $citationEvaluator;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var PDFService
     */
    private $PDFService;
    /**
     * @var NormalizationFactorEvaluator
     */
    private $normalizationFactorEvaluator;

    /**
     * @var EntityManager
     */
    private $entityManager;


    public function __construct(
        ArticleEvaluator $articleEvaluator,
        BookEvaluator $bookEvaluator,
        ActivityEvaluator $activityEvaluator,
        PatentEvaluator $patentEvaluator,
        ProjectEvaluator $projectEvaluator,
        CitationEvaluator2 $citationEvaluator,
         NormalizationFactorEvaluator $normalizationFactorEvaluator,
        \Swift_Mailer $mailer,
        PDFService $PDFService,
        EntityManagerInterface $entityManager
    )
    {
        $this->articleEvaluator = $articleEvaluator;
        $this->bookEvaluator = $bookEvaluator;
        $this->activityEvaluator = $activityEvaluator;
        $this->patentEvaluator = $patentEvaluator;
        $this->projectEvaluator = $projectEvaluator;
        $this->citationEvaluator=$citationEvaluator;
        $this->mailer=$mailer;
        $this->PDFService=$PDFService;
        $this->normalizationFactorEvaluator = $normalizationFactorEvaluator;
        $this->entityManager=$entityManager;
    }

    /**
     * @Route("/", name="evaluation_index")
     */
    public function index()
    {
        return $this->render('evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
        ]);
    }


    /**
     * @Route("/user/{user_id}/{fullName}", name="evaluation_show", methods={"GET"})
     */
    public function show(Request $request, $user_id,$fullName)
    {
        $year = $request->query->get('year');

        if (!is_null($year)) {

            return $this->render('evaluation/show.html.twig', [
                'user_id'=>$user_id,
                'user_full_name' => $fullName,
                 'evaluation'=>$this->getEvaluation($user_id,$year)
            ]);
        } else {
            return $this->render('evaluation/show.html.twig', [
                'user_full_name' => $fullName,
            ]);
        }
    }

    /**
     * @Route("/download-pdf/{id}/{year}/{action}", name="download-evaluation")
     */
    public function download(Request $request,User $user,$year,$action)
    {


        $html = $this->renderView('evaluation/pdf/pdf_template.hml.twig', [
            'user_full_name' => $user,
            'evaluation'=>$this->getEvaluation($user->getId(),$year)
        ]);


       /* $imgPath=sprintf("%s/screenshots/%s.png",
            $this->getParameter('kernel.project_dir'),
            $user->getEmail()
            );*/
        //$imgPath=$this->getParameter('kernel.project_dir')."/screenshots/'.$user->getEmail().'.png';
        $pdfName=$user.".pdf";

        $pdf=$this->PDFService->getPDF($html,null);

        if($action==='download_pdf'){
            $response=new Response($pdf);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $pdfName
            );
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        }elseif($action="send_email"){
           /* $message = (new \Swift_Message('Evaluare profesionala'))
            ->setFrom('ion.ivan@infim.ro')
                ->setTo($user->getEmail());
            $message->setBody(
                $this->renderView('evaluation/email/template.html.twig',[])
            );
            $message->attach( new \Swift_Attachment($pdf, 'evaluare_profesionala.pdf', 'application/pdf'));
            $status=$this->mailer->send($message);
            if($status===1){
                $this->addEmailToken($user);
            }
            return new Response($status);*/
           $email=new \SendGrid\Mail\Mail();
           $email->setFrom("ion.ivan@infim.ro","Ion Ivan");
           $email->setSubject("Rezultate finale, evaluare profesionala NIMP 2021");

           $email->addTo($user->getEmail(),"Nimp user");
           $email->addContent("text/html",$this->renderView('evaluation/email/template.html.twig',[]));

           $att= new \SendGrid\Mail\Attachment();
           $att->setContent($pdf);
           $att->setType("application/pdf");
           $att->setFilename($pdfName);
           $att->setDisposition("attachment");
           $email->addAttachment($att);


            $sendgrid=new \SendGrid('enter here password');

           try{
               $response=$sendgrid->send($email);
               if($response->statusCode()===202){
                   $this->addEmailToken($user);
               }
               return new Response($response->statusCode().PHP_EOL.$response->body());
           }catch(\Exception $ex){
               return new Response($ex->getMessage());
           }
        }

    }

    public function addEmailToken($user)
    {
         $token=$this->entityManager->getRepository(Settings::class)->findOneBy([
             'name'=>'EMAIL_TOKEN'
         ]);
         if($token){
             $user->setEmailToken($token->getValue());
             $this->entityManager->persist($user);
             $this->entityManager->flush();
         }
    }

    public function getEvaluation(int $userId,int $year):array
    {

        return [
            'articles'=>$this->articleEvaluator->getEvaluation($userId, $year),
            'books'=>$this->bookEvaluator->getEvaluation($userId,$year),
            'patents'=>$this->patentEvaluator->getEvaluation($userId,$year),
             'projects'=>$this->projectEvaluator->getEvaluation($userId,$year),
            'activities'=>$this->activityEvaluator->getEvaluation($userId,$year),
            'articlesWithCitations'=>$this->citationEvaluator->getEvaluation($userId,$year),
             'factor'=>$this->normalizationFactorEvaluator->getEvaluation($userId,$year),
            'year'=>$year
        ];
    }
}

/**
 *
it worked adding these two lines $options['ssl']['verify_peer'] = FALSE; $options['ssl']['verify_peer_name'] = FALSE;
 * In this file: vendor\swiftmailer\swiftmailer\lib\classes\Swift\Transport\StreamBuffer.php in the function _establishSocketConnection(), but of course I won't do this in production server
 */