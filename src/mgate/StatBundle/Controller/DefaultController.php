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
            array("name" => "Data Serie Name",    "data" => array(1,2,4,5,6,3,8))
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
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->getEtudesCa();
        
        $data = array();
        $cumul=0;
        foreach ($etudes as $etude) {

            if($etude->getCc())
            {
                if($etude->getCc()->getDateSignature() )
                {
                    $cumul+= $etudeManager->getTotalHT($etude);
                    
                    $data[]= array( "x"=>$etude->getCc()->getDateSignature()->getTimestamp()*1000,
                                    "y"=>$cumul, "name"=>$etudeManager->getRefEtude($etude)." - ".$etude->getNom(),
                                    'date'=>$etude->getCc()->getDateSignature()->format('d/m/Y'),
                                    'prix'=>$etudeManager->getTotalHT($etude));
                    
                }
            }
       }
        
        
        
        // Chart
        $series = array(
            array("name" => "Chiffre d'Affaire total",    "data" => $data)
        );

        $ob = new Highchart();
        $ob->global->useUTC(false);
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Chiffre d\'Affaire titre du graph');
        $ob->xAxis->title(array('text'  => "Date"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Chiffre d'Affaire cumulé"));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} le {point.date}<br />{point.name} à {point.prix} €');
        $ob->series($series);

        return $this->render('mgateStatBundle:Default:ca.html.twig', array(
            'chart' => $ob
        ));
    }
}
