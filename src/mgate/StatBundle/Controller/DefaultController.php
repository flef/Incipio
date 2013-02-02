<?php

namespace mgate\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ob\HighchartsBundle\Highcharts\Highchart;

class DefaultController extends Controller
{
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
    
    public function caAction()
    {
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        $ca = array();
        $cumul=0;
        foreach ($etudes as $etude) {
            $cumul+= $this->get('mgate.etude_manager')->getTotalHT($etude);
            $value = array();
            $value[]=$etude->getDateCreation()->getTimestamp();
            $value[]=$cumul;
            $ca[] = $value;
       }
        
        
        
        // Chart
        $series = array(
            array("name" => "Chiffre d'Affaire total",    "data" => $ca)
        );

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Chiffre d\'Affaire titre du graph');
        $ob->xAxis->title(array('text'  => "Horizontal axis title"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Vertical axis title"));
        $ob->series($series);

        return $this->render('mgateStatBundle:Default:ca.html.twig', array(
            'chart' => $ob
        ));
    }
}
