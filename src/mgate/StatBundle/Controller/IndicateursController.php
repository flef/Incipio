<?php

namespace mgate\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ob\HighchartsBundle\Highcharts\Highchart;

use mgate\SuiviBundle\Entity\EtudeRepository;

// A externaliser dans les parametres
define("STATE_ID_EN_COURS", 2);
define("STATE_ID_TERMINEE",4);

class Indicateur{
    private $titre;
    private $methode;
    public function getTitre() { return $this->titre; } 
    public function getMethode() { return $this->methode; } 
    public function setTitre($x) { $this->titre = $x; return $this; } 
    public function setMethode($x) { $this->methode = $x; return $this; } 
}


class IndicateursController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $indicateursSuivi = array();
        $indicateursGestion = array();
        $indicateursRFP = array();
        $indicateursTreso = array();
        $indicateursCom = array();
        
        $chiffreAffaires = new Indicateur();
        $chiffreAffaires->setTitre('Evolution du Chiffre d\'Affaires')
                        ->setMethode('getCA');

        $indicateursSuivi[] = $chiffreAffaires;
        
        $ressourcesHumaines = new Indicateur();
        $ressourcesHumaines->setTitre('Evolution RH')
                        ->setMethode('getRh');
        $indicateursSuivi[] = $ressourcesHumaines;
        
        return $this->render('mgateStatBundle:Indicateurs:index.html.twig',
                array('indicateursSuivi' => $indicateursSuivi,
                    'indicateursRfp' => $indicateursRFP,
                    'indicateursGestion' => $indicateursGestion,
                    'indicateursCom' => $indicateursCom,
                    'indicateursTreso' => $indicateursTreso,
                    ));
    }
    

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function ajaxAction()
    {
        $request = $this->get('request');

        if($request->getMethod() == 'GET')
        {
            $chartMethode = $request->query->get('chartMethode');
            
            if($chartMethode)      
                return $this->$chartMethode();
        }
        return $this->render('mgateStatBundle:Indicateurs:index.html.twig', array('indicateurs' => array()));
    }
    
    
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */    
    public function getCA()
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
        
        
        
        //WARN :::
        
        $ob->chart->renderTo('getCA');  // The #id of the div where to render the chart
        
        ///
        
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
        return $this->render('mgateStatBundle:Indicateurs:indicateur.html.twig', array(    
            'chart' => $ob
        ));
    }
    
    
        /**
     * @Secure(roles="ROLE_CA")
     */    
    public function getRh()
    {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $missions = $this->getDoctrine()->getManager()->getRepository('mgateSuiviBundle:Mission')->findBy(array(), array('debutOm' => 'asc'));
        
        //$data = array();
        $mandats = array();
        $maxMandat = $etudeManager->getMaxMandatCc();

        $cumuls=array();
        for($i=0 ; $i<=$maxMandat ; $i++) 
            $cumuls[$i] = 0;
        
        $mandats[1]=array();

        //Etape 1 remplir toutes les dates
        foreach ($missions as $mission) {
            $etude = $mission->getEtude();
            $dateDebut = $mission->getdebutOm();
            $dateFin = $mission->getfinOm();

            if($dateDebut && $dateFin)
            {
                $idMandat=$etudeManager->dateToMandat($dateDebut);

                $cumuls[0]++;

                $interval = new \DateInterval('P'.($maxMandat-$idMandat).'Y');
                $dateDebutDecale = clone $dateDebut;
                //$dateDebutDecale->add($interval);
                $dateFinDecale = clone $dateFin;
                //$dateFinDecale->add($interval);

                $addDebut = true;
                $addFin = true;
                foreach($mandats[1] as $datePoint)
                {
                    if(($dateDebutDecale->getTimestamp()*1000) == $datePoint['x'] )
                        $addDebut=false;
                    if(($dateFinDecale->getTimestamp()*1000) == $datePoint['x'] )
                        $addFin=false;
                        
                }
                
                if($addDebut)
                {
                    $mandats[1][]
                           = array( "x"=>$dateDebutDecale->getTimestamp()*1000,
                                    "y"=>0/*$cumuls[0]*/, "name"=>$etudeManager->getRefEtude($etude)." + ".$etude->getNom(),
                                    'date'=>$dateDebutDecale->format('d/m/Y'),
                                    'prix'=>$etudeManager->getTotalHT($etude));
                }
                if($addFin)
                {
                    $mandats[1][]
                           = array( "x"=>$dateFinDecale->getTimestamp()*1000,
                                    "y"=>0/*$cumuls[0]*/, "name"=>$etudeManager->getRefEtude($etude)." - ".$etude->getNom(),
                                    'date'=>$dateDebutDecale->format('d/m/Y'),
                                    'prix'=>$etudeManager->getTotalHT($etude));
                }

            }
        }
        
        //Etapes 2 trie dans l'ordre
        $callback = function($a, $b) use($mandats) {
            return $mandats[1][$a]['x']>$mandats[1][$b]['x'];
        };
        uksort($mandats[1], $callback);
        foreach($mandats[1] as $entree)
            $mandats[2][] = $entree;
        $mandats[1]=array();
        
        //Etapes 3 ++ --
        foreach ($missions as $mission) {
            $etude = $mission->getEtude();
            $dateFin = $mission->getfinOm();
            $dateDebut = $mission->getdebutOm();

            if($dateDebut && $dateFin)
            {
                $idMandat=$etudeManager->dateToMandat($dateFin);

                $interval = new \DateInterval('P'.($maxMandat-$idMandat).'Y');
                $dateDebutDecale = clone $dateDebut;
                //$dateDebutDecale->add($interval);
                $dateFinDecale = clone $dateFin;
                //$dateFinDecale->add($interval);
                
                foreach($mandats[2] as &$entree)
                {
                    if($entree['x'] >= $dateDebutDecale->getTimestamp()*1000 && $entree['x'] < $dateFinDecale->getTimestamp()*1000)
                    {
                        $entree['y']++;
                    }
                }
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
        
        
        
        //WARN :::
        
        $ob->chart->renderTo('getRh');  // The #id of the div where to render the chart
        
        ///
        $ob->chart->type("spline");
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
        return $this->render('mgateStatBundle:Indicateurs:indicateur.html.twig', array(    
            'chart' => $ob
        ));
    }

}