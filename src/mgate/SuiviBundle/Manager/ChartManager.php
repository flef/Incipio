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
        $mort =  new \DateTime();
        
        //Contacts Client
        if(count($etude->getClientContacts())!=0 && $type=="suivi")
        {
            foreach($etude->getClientContacts() as $contact)
            {
                $date = $contact->getDate();
                if($naissance >= $date) $naissance= clone $date;
                if($mort <= $date) $mort= clone $date;
                
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
                if($naissance >= $date) $naissance= clone $date;
                if($mort <= $date) $mort= clone $date;
                
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                        "titre"=>"Avant-Projet", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'square', 'fillColor'=>'blue'));
                $naissance= clone $etude->getAp()->getDateSignature();
            }
            $data = $dataSauv;
            if($etude->getCc() && $etude->getCc()->getDateSignature() )
            {
                $date = $etude->getCc()->getDateSignature();
                if($naissance >= $date) $naissance= clone $date;
                if($mort <= $date) $mort= clone $date;
                
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                    "titre"=>"Convention Client", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'triangle', 'fillColor'=>'red'));
            }
            $data = $dataSauv;
            if($etude->getPvr() && $etude->getPvr()->getDateSignature() )
            {
                $date = $etude->getPvr()->getDateSignature();
                if($naissance >= $date) $naissance= clone $date;
                if($mort <= $date) $mort= clone $date;
                
                $data[] = array("x" => count($cats), "y" => $date->getTimestamp()*1000,
                        "name"=>"Procès Verbal de Recette", "detail"=>"signé le ".$date->format('d/m/Y'));
                $series[] = array("type"=> "scatter", "data" => $data, "marker"=>array('symbol'=>'circle'));
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

                $data[] = array("low" => $debut->getTimestamp()*1000, "y" => $fin->getTimestamp()*1000, 'color'=>'#005CA4',
                        "titre"=>"Durée de déroulement des phases", "detail"=>"du ".$debut->format('d/m/Y')." au ".$fin->format('d/m/Y') );
    
                $cats[] = "Etude";
            }
        }
        
        foreach($etude->getPhases() as $phase)
        {
            if($phase->getDateDebut()&&$phase->getDelai())
            {
                $debut = $phase->getDateDebut();
                if($naissance >= $debut) $naissance= clone $debut;

                $fin = clone $debut;
                $fin->add(new \DateInterval('P'.$phase->getDelai().'D'));
                if($mort <= $fin) $mort= clone $fin;
                
                $func = new \Zend\Json\Expr("function() {return this.point.titre;}");
                $data[] = array("low" => $fin->getTimestamp()*1000, "y" => $debut->getTimestamp()*1000,
                    "titre"=>$phase->getTitre(), "detail"=>"du ".$debut->format('d/m/Y')." au ".$fin->format('d/m/Y'), 'color'=>'#F26729',
                        'dataLabels'=>array('enabled'=>true, 'align'=>'left', 'verticalAlign'=>'bottom', 'formatter'=> $func, 'y'=>-10));
            }
            else
                $data[] = array();
            
            $cats[] = "Phase n°".($phase->getPosition()+1);            
            
        }
        $series[] = array("type"=> "bar", "data" => $data);
        
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
            
            $series[] = array("type"=> "spline", "data" => $data, "marker"=>array('radius'=>1, 'color'=>'#545454'), 'color'=>'#545454', 'lineWidth'=>1, 'pointWidth'=>5);
        }

        $style=array('color'=>'#000000', 'fontSize'=>'11px', 'fontFamily'=>'Calibri (Corps)');

        $ob = new Highchart();
        $ob->global->useUTC(false);
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart 
        $ob->chart->height(100+count($etude->getPhases())*25);
        $ob->title->text('');
        $ob->xAxis->title(array('text'  => ""));
        $ob->xAxis->categories($cats);
        $ob->xAxis->labels(array('style'=>$style));
        $ob->yAxis->title(array('text' => ''));
        $ob->yAxis->type('datetime');
        $ob->yAxis->min($naissance->sub(new \DateInterval('P1D'))->getTimestamp()*1000);
        $ob->yAxis->max($mort->add(new \DateInterval('P1D'))->getTimestamp()*1000);
        $ob->yAxis->labels(array('style'=>$style));
        $ob->chart->zoomType('y');
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);
        $ob->exporting->enabled(false);
        $ob->plotOptions->series(array('pointPadding'=>0, 'groupPadding'=>0, 'pointWidth'=>10,'groupPadding'=>0,'marker'=>array('radius'=>5), 'tooltip'=>array('pointFormat'=>'<b>{point.titre}</b><br /> {point.detail}')));
        $ob->plotOptions->scatter(array('tooltip'=>array('headerFormat'=>'')));
        $ob->series($series);
        
        return $ob;
    }
}