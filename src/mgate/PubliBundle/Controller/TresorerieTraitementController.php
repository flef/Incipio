<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PubliBundle\Controller\TraitementController;

class TresorerieTraitementController extends TraitementController{
    /*
    private function publipostage($doc, $id) {
        $em = $this->getDoctrine()->getEntityManager();
        // Abréviation a externaliser dans traitement controler
        if($doc == 'NF'){
            if(!$nf = $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id))
                throw $this->createNotFoundException('La Note de Frais n\'existe pas !');
            $champs = $this->getChampsNF($nf);
            
        }
        
        
        
        
        
        
        
        
        $chemin = $this->getDoctypeAbsolutePathFromName($doc);
        $nombreRepeat = array(count($etude->getPhases()), count($etude->getMissions()));
        $aides = array();


        //DEBUG   
        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path . $doc . '.docx';
        }

        $templatesXMLtraite = $this->traiterTemplates($chemin, $nombreRepeat, $champs);
        $champsBrut = $this->verifierTemplates($templatesXMLtraite);


        $repertoire = 'tmp';

        //SI DM on prend la ref de RM et ont remplace RM par DM
        if ($doc == 'DM') {
            $doc = 'RM';
            $isDM = true;
        }

        if ($etude->getDoc($doc, $key))
            $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc, $key);
        else
            $refDocx = 'ERROR';

        //On remplace DM par RM si DM
        if (isset($isDM) && $isDM)
            $refDocx = preg_replace("#RM#", 'DM', $refDocx);

        $idDocx = $refDocx . '-' . ((int) strtotime("now") + rand());
        copy($chemin, $repertoire . '/' . $idDocx);
        $zip = new \ZipArchive();
        $zip->open($repertoire . '/' . $idDocx);

        $images = array();
        //Gantt
        if ($doc == 'AP' || (isset($isDM) && $isDM)) {
            $chartManager = $this->get('mgate.chart_manager');
            $ob = $chartManager->getGantt($etude, $doc);
            if ($chartManager->exportGantt($ob, $idDocx)) {
                $image = array();
                $image['fileLocation'] = "$repertoire/$idDocx.png";
                $info = getimagesize("$repertoire/$idDocx.png");
                $image['width'] = $info[0];
                $image['height'] = $info[1];
                $images['imageVARganttAP'] = $image;
            }
        }

        //Intégration temporaire
        $imagesInDocx = $this->traiterImages($templatesXMLtraite, $images);
        foreach ($imagesInDocx as $image) {
            $zip->deleteName('word/media/' . $image[2]);
            $zip->addFile($repertoire . '/' . $idDocx . '.png', 'word/media/' . $image[2]);
        }

        foreach ($templatesXMLtraite as $templateXMLName => $templateXMLContent) {
            $zip->deleteName('word/' . $templateXMLName);
            $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
        }






        $zip->close();

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;


        return array($champsBrut, $aides);
    }
     * 
     */
}


