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
        $maxMandat = $etudeManager->getMaxMandatCc();

        $cumuls=array();
        for($i=0 ; $i<=$maxMandat ; $i++) 
            $cumuls[$i] = 0;

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();

            if($dateSignature)
            {
                $idMandat=$etudeManager->dateToMandat($dateSignature);
                
                $cumuls[$idMandat] += $etudeManager->getTotalHT($etude);

                $interval = new \DateInterval('P'.($maxMandat-$idMandat).'Y');
                $dateDecale = clone $dateSignature;
                $dateDecale->add($interval);

                $mandats[$idMandat][]
                       = array( "x"=>$dateDecale->getTimestamp()*1000,
                                "y"=>$cumuls[$idMandat], "name"=>$etudeManager->getRefEtude($etude)." - ".$etude->getNom(),
                                'date'=>$dateDecale->format('d/m/Y'),
                                'prix'=>$etudeManager->getTotalHT($etude));

            }
       }
        
        
        
        // Chart
        $series = array();
        foreach ($mandats as $idMandat => $data)
        {
            if($idMandat>=4)
            $series[] = array("name" => "Mandat ".$idMandat." - ".$etudeManager->mandatToString($idMandat), "data" => $data);
        }

        $style=array('color'=>'#000000', 'fontWeight'=>'bold', 'fontSize'=>'16px');

        $ob = new Highchart();
        $ob->global->useUTC(false);
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->xAxis->labels(array('style'=>$style));
        $ob->yAxis->labels(array('style'=>$style));
        $ob->title->text('Évolution par mandat du chiffre d\'affaire signé cumulé');
        $ob->title->style(array('fontWeight'=>'bold', 'fontSize'=>'20px'));
        $ob->xAxis->title(array('text'  => "Date", 'style'=>$style));
        $ob->xAxis->type('datetime');
        $ob->yAxis->min(0);
        $ob->yAxis->title(array('text'  => "Chiffre d'Affaire signé cumulé", 'style'=>$style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} le {point.date}<br />{point.name} à {point.prix} €');
        $ob->credits->enable(false);
        $ob->legend->floating(true);
        $ob->legend->layout('vertical');
        $ob->legend->y(40);
        $ob->legend->x(-40);
        $ob->legend->verticalAlign('top');
        $ob->legend->reversed(true);
        $ob->legend->align('right');
        $ob->legend->backgroundColor('#FFFFFF');
        $ob->legend->itemStyle($style);
        $ob->plotOptions->series(array('lineWidth'=>5, 'marker'=>array('radius'=>8)));
        $ob->series($series);

        //return $this->render('mgateStatBundle:Default:ca.html.twig', array(
        return $this->render('mgateStatBundle:Default:caFull.html.twig', array(    
            'chart' => $ob
        ));
    }
}
