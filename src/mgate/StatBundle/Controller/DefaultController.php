<?php

namespace mgate\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ob\HighchartsBundle\Highcharts\Highchart;

use mgate\SuiviBundle\Entity\EtudeRepository;

// A externaliser dans les parametres
define("STATE_ID_EN_COURS", 2);
define("STATE_ID_TERMINEE",4);

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
            $signee = $etude->getStateID() == STATE_ID_EN_COURS
                   || $etude->getStateID() == STATE_ID_TERMINEE;

            if($dateSignature && $signee)
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
            //if($idMandat>=4)
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
        $ob->xAxis->dateTimeLabelFormats(array('month'  => "%b"));
        $ob->yAxis->min(0);
        $ob->yAxis->title(array('text'  => "Chiffre d'Affaire signé cumulé", 'style'=>$style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} le {point.date}<br />{point.name} à {point.prix} €');
        $ob->credits->enabled(false);
        $ob->legend->floating(true);
        $ob->legend->layout('vertical');
        $ob->legend->y(40);
        $ob->legend->x(90);
        $ob->legend->verticalAlign('top');
        $ob->legend->reversed(true);
        $ob->legend->align('left');
        $ob->legend->backgroundColor('#FFFFFF');
        $ob->legend->itemStyle($style);
        $ob->plotOptions->series(array('lineWidth'=>5, 'marker'=>array('radius'=>8)));
        $ob->series($series);

        //return $this->render('mgateStatBundle:Default:ca.html.twig', array(
        return $this->render('mgateStatBundle:Default:caFull.html.twig', array(    
            'chart' => $ob
        ));
    }
    
    public function tauxConversionAction()
    {
        $etudeManager = $this->get('mgate.etude_manager');
        $tauxConversion = $etudeManager->getTauxConversion();
        $data_final = array();
        $categories = array();
        
        $series = array();
        foreach ($tauxConversion as $idMandat => $data)
        {
            $data_final = $data['ap_signe']/$data['ap_redige']*100;
            $series[] = array("name" => "Mandat ".$idMandat." - ".$etudeManager->mandatToString($idMandat), "data" => $data_final);
            array_push($categories,"Mandat ".$data['mandat']);
        }
        //var_dump($categories);
        var_dump($series);
   
        $style=array('color'=>'#000000', 'fontWeight'=>'bold', 'fontSize'=>'16px');

        $ob = new Highchart();
        //$ob->global->useUTC(false);
        $ob->chart->type('column');
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        //$ob->xAxis->labels(array('style'=>$style));
        //$ob->yAxis->labels(array('style'=>$style));
        $ob->title->text('Évolution par mandat du taux de conversion');
        $ob->title->style(array('fontWeight'=>'bold', 'fontSize'=>'20px'));
        //$ob->xAxis->title(array('text'  => null, 'style'=>$style));
        //$ob->xAxis->type('datetime');
        $ob->xAxis->categories($categories);
        $ob->yAxis->min(0);
        $ob->yAxis->title(array('text'  => "Taux de conversion"));
        $ob->tooltip->headerFormat('<span style="font-size:10px">{point.key}</span><table>');
        $ob->tooltip->pointFormat('<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>');
        $ob->tooltip->footerFormat('</table>');
        $ob->tooltip->shared(true);
        $ob->tooltip->useHTML(true);
        //$ob->credits->enabled(false);
        //$ob->legend->floating(true);
        //$ob->legend->layout('vertical');
        //$ob->legend->y(100);
        //$ob->legend->x(-100);
        //$ob->legend->verticalAlign('top');
        //$ob->legend->reversed(true);
        //$ob->legend->align('right');
        $ob->legend->backgroundColor('#FFFFFF');
        //$ob->legend->shadow(true);
        //$ob->legend->borderWidth(1);
        //$ob->legend->itemStyle($style);
        $ob->plotOptions->column(array('pointPadding'=>0.2,'borderWidth'=>0));
        $ob->series($series);

        //return $this->render('mgateStatBundle:Default:ca.html.twig', array(
        return $this->render('mgateStatBundle:Default:caFull.html.twig', array(    
            'chart' => $ob
        ));
    }
}
