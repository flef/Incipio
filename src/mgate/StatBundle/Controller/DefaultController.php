<?php

namespace mgate\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ob\HighchartsBundle\Highcharts\Highchart;

use mgate\SuiviBundle\Entity\EtudeRepository;

class DefaultController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction()
    {
        // Chart
        $series = array(
            array("name" => "Data Serie Name",    "data" => array(1,2,4,5,6,3,8)) ,
            array("name" => "CCCCCe",    "data" => array(1,7,12,14,15,3,8))
        );

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Chart Title');
        $ob->xAxis->title(array('text'  => "Horizontal axis title"));
        $ob->yAxis->title(array('text'  => "Vertical axis title"));
        $ob->series($series);

        return $this->render('mgateStatBundle:Default:test.html.twig', array(
            'chart' => $ob
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function caAction()
    {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $Ccs = $this->getDoctrine()->getManager()->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));
        
        //$data = array();
        $mandats = array(); 
        $maxMandat = $etudeManager->getMaxMandat();
        $cumuls=array();
        for($i=0 ; $i<=$maxMandat ; $i++) 
            $cumuls[$i] = 0;

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();

            if($etude->getCc())
            {
                if($etude->getCc()->getDateSignature() )
                {
                    $cumuls[$etude->getMandat()] += $etudeManager->getTotalHT($etude);
                    
                    $interval = new \DateInterval('P'.($maxMandat-$etude->getMandat()).'Y');
                    $dateDecale = $etude->getCc()->getDateSignature()->add($interval);
                    
                    $mandats[$etude->getMandat()][]
                           = array( "x"=>$dateDecale->getTimestamp()*1000,
                                    "y"=>$cumuls[$etude->getMandat()], "name"=>$etudeManager->getRefEtude($etude)." - ".$etude->getNom(),
                                    'date'=>$dateDecale->format('d/m/Y'),
                                    'prix'=>$etudeManager->getTotalHT($etude));
                    
                }
            }
       }
        
        
        
        // Chart
        $series = array();
        foreach ($mandats as $idMandat => $data)
        {
            $series[] = array("name" => "Mandat ".$idMandat." - ".$etudeManager->mandatToString($idMandat),    "data" => $data);
        }

        

        $ob = new Highchart();
        $ob->global->useUTC(false);
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Évolution par mandat du chiffre d\'affaire signé cumulé');
        $ob->xAxis->title(array('text'  => "Date"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Chiffre d'Affaire signé cumulé"));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} le {point.date}<br />{point.name} à {point.prix} €');
        $ob->series($series);

        return $this->render('mgateStatBundle:Default:ca.html.twig', array(
            'chart' => $ob
        ));
    }
}
