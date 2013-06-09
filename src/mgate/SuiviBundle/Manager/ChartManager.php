<?php

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Manager\BaseManager;
use mgate\SuiviBundle\Entity\Etude as Etude;
use Ob\HighchartsBundle\Highcharts\Highchart;

class ChartManager /*extends \Twig_Extension*/ {

    protected $em;
    protected $tva;
    protected $etudeManager;

    public function __construct(EntityManager $em, $tva, EtudeManager $etudeManager) {
        $this->em = $em;
        $this->tva = $tva;
        $this->etudeManager = $etudeManager;

    }
   
    /**
    * Taux de conversion
    */
    public function getGantt(Etude $etude, $type)
    {
      
        // Chart
        $series = array();
        $data = array();
        $cats = array();
        $naissance =  new \DateTime();
        
        //Contacts Client
        if(count($etude->getClientContacts())!=0 && $type=="suivi")
        {
            foreach($etude->getClientContacts() as $contact)
            {
                $date = $contact->getDate();
                if($naissance >= $date)
                    $naissance= clone $date;
                
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                    "titre"=>$contact->getObjet(), "detail"=>"fait par ".$contact->getFaitPar()->getPrenomNom()." le ".$date->format('d/m/Y') );
            }
            $series[] = array("type"=> "scatter", "data" => $data);
            $cats[] = "Contact client";
        }
              
        //Documents
        if($type=="suivi")
        {
            $data = array();
            for($j=0;$j<count($cats);$j++)
                $data[]=array();
            $dataSauv= $data;
            
            if($etude->getAp()&& $etude->getAp()->getDateSignature())
            {
                $date = $etude->getAp()->getDateSignature();
                if($naissance >= $date)
                    $naissance= clone $date;
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                        "titre"=>"Avant-Projet", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'circle'));
                $naissance= clone $etude->getAp()->getDateSignature();
            }
            $data = $dataSauv;
            if($etude->getCc() && $etude->getCc()->getDateSignature() )
            {
                $date = $etude->getCc()->getDateSignature();
                if($naissance >= $date)
                    $naissance= clone $date;
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                    "titre"=>"Convention Client", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'square'));
            }
            $data = $dataSauv;
            if($etude->getPvr() && $etude->getPvr()->getDateSignature() )
            {
                $date = $etude->getPvr()->getDateSignature();
                if($naissance >= $date)
                    $naissance= clone $date;
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                        "titre"=>"Procès Verbal de Recette", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'triangle'));
            }
            $cats[] = "Documents";
        }
        
        //Etude
        if($type=="suivi")
        {
            $data = array();
            for($j=0;$j<count($cats);$j++)
                $data[]=array();
            
            if($this->etudeManager->getDateLancement($etude)&&$this->etudeManager->getDateFin($etude))
            {
                $debut = $this->etudeManager->getDateLancement($etude);
                $fin = $this->etudeManager->getDateFin($etude);

                $data[] = array("low" => $debut->getTimestamp()*1000, "y" => $fin->getTimestamp()*1000,
                        "titre"=>"Durée de déroulement des phases", "detail"=>"du ".$debut->format('d/m/Y')." au ".$fin->format('d/m/Y') );

                $series[] = array("type"=> "bar", "data" => $data, 'color'=>'#005CA4');     
                $cats[] = "Etude";
            }
        }
        
        //Phases
        $data = array();
        for($j=0;$j<count($cats);$j++)
            $data[]=array();
        foreach($etude->getPhases() as $phase)
        {
            if($phase->getDateDebut()&&$phase->getDelai())
            {
                $debut = $phase->getDateDebut();
                if($naissance >= $debut)
                    $naissance= clone $debut;
                $fin = clone $debut;
                $fin->add(new \DateInterval('P'.$phase->getDelai().'D'));
                $data[] = array("low" => $debut->getTimestamp()*1000, "y" => $fin->getTimestamp()*1000,
                    "titre"=>$phase->getTitre(), "detail"=>"du ".$debut->format('d/m/Y')." au ".$fin->format('d/m/Y') );
            }
            else
                $data[] = array();
            
            $cats[] = "Phase n°".($phase->getPosition()+1);            
            
        }
        $series[] = array("type"=> "bar", "data" => $data, 'color'=>'#F26729');
        
        //Today, à faire à la fin
        $data = array();
        if($type=="suivi")
        {
            $date = new \DateTime('NOW');
            //if($naissance >= $date)
                //$naissance= clone $date;
            $data[] = array("x" => 0, "y" => $date->getTimestamp()*1000,
                "titre"=>"aujourd'hui", "detail"=>"le ".$date->format('d/m/Y') );
            $data[] = array("x" => count($cats)-1, "y" => $date->getTimestamp()*1000,
                "titre"=>"aujourd'hui", "detail"=>"le ".$date->format('d/m/Y') );
            
            $series[] = array("type"=> "spline", "data" => $data, "marker"=>array('radius'=>1, 'color'=>'#545454'), 'color'=>'#545454', 'lineWidth'=>1);
        }

        $style=array('color'=>'#000000', 'fontSize'=>'11px', 'fontFamily'=>'Calibri (Corps)');

        $ob = new Highchart();
        $ob->global->useUTC(false);
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart 
        $ob->title->text('');
        $ob->xAxis->title(array('text'  => ""));
        $ob->xAxis->categories($cats);
        $ob->xAxis->labels(array('style'=>$style));
        $ob->yAxis->title(array('text' => ''));
        $ob->yAxis->type('datetime');
        $ob->yAxis->min($naissance->sub(new \DateInterval('P1D'))->getTimestamp()*1000);
        $ob->yAxis->labels(array('style'=>$style));
        //$ob->tooltip->headerFormat('<b>{series.name} : {point.titre}</b><br />');
        //$ob->tooltip->pointFormat('<b>{point.titre}sdfqsdf</b> <br /> fait par {point.faitPar} le {point.periode}');
        $ob->chart->zoomType('y');
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);
        $ob->plotOptions->series(array('marker'=>array('radius'=>5), 'tooltip'=>array('pointFormat'=>'<b>{point.titre}</b><br /> {point.detail}')));
        $ob->series($series);
        
        return $ob;
    }
}