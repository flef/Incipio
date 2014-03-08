<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;



class TraitementController extends Controller {

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function publiposterAction($templateName, $rootName, $rootObject_id){
        $this->publipostage($templateName, $rootName, $rootObject_id);
        return $this->telechargerAction($templateName);
    }

    private function publipostage($templateName, $rootName, $rootObject_id){
        $em = $this->getDoctrine()->getManager();    
        
        switch ($rootName) {
            case 'etude':
                if (!$rootObject = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                if($this->get('mgate.etude_manager')->confidentielRefus($rootObject, $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            case 'etudiant':
                if (!$rootObject = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                break;
            case 'mission':
                if (!$rootObject = $em->getRepository('mgate\SuiviBundle\Entity\Mission')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                break;
            case 'facture':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\Facture')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                if($rootObject->getEtude() && $this->get('mgate.etude_manager')->confidentielRefus($rootObject->getEtude(), $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            case 'nf':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\NoteDeFrais')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                break;
            case 'bv':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\BV')->find($rootObject_id))
                    throw $this->createNotFoundException('Entity[id=' . $rootObject_id. '] inexistant');
                if($rootObject->getMission() && $rootObject->getMission()->getEtude() && $this->get('mgate.etude_manager')->confidentielRefus($rootObject->getMission()->getEtude(), $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            default:
                throw $this->createNotFoundException('Publipostage invalid ! Pas de bol...');
                break;
        }

        $chemin = $this->getDoctypeAbsolutePathFromName($templateName);
        
        //DEBUG   
        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path.$chemin;
        }
        
        $templatesXMLtraite = $this->traiterTemplates($chemin, $rootName, $rootObject);
        $repertoire = 'tmp';

        //SI DM on prend la ref de RM et ont remplace RM par DM
        if ($templateName == 'DM') {
            $templateName = 'RM';  $isDM = true;
        }

        if ($rootName == 'etude' && $rootObject->getReference())// TODO IMPLEMENT getref
            $refDocx = $rootObject->getReference();
        elseif ($rootName == 'etudiant')
            $refDocx = $templateName.'-'.$rootObject->getIdentifiant();
        else
            $refDocx = 'UNREF';
            

        //On remplace DM par RM si DM
        if (isset($isDM) && $isDM)
            $refDocx = preg_replace("#RM#", 'DM', $refDocx);

        $idDocx = $refDocx . '-' . ((int) strtotime("now") + rand());
        copy($chemin, $repertoire . '/' . $idDocx);
        $zip = new \ZipArchive();
        $zip->open($repertoire . '/' . $idDocx);

        
        /*
         * TRAITEMENT INSERT IMAGE
         */
        $images = array();
        //Gantt
        if ($templateName == 'AP' || (isset($isDM) && $isDM)) {
            $chartManager = $this->get('mgate.chart_manager');
            $ob = $chartManager->getGantt($rootObject, $templateName);
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
        /*****/

        foreach ($templatesXMLtraite as $templateXMLName => $templateXMLContent) {
            $zip->deleteName('word/' . $templateXMLName);
            $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
        }
        
        $zip->close();

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;
    }
     
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function telechargerAction($templateName) {
        $junior = $this->container->getParameter('junior');
        $this->purge();
        if (isset($_SESSION['idDocx']) && isset($_SESSION['refDocx'])) {
            $idDocx = $_SESSION['idDocx'];
            $refDocx = $_SESSION['refDocx'];   
            
            $templateName = 'tmp/' . $idDocx;
            header('Content-Type: application/msword');
            header('Content-Length: ' . filesize($templateName));
            header('Content-disposition: attachment; filename=' .$junior['tag']. $refDocx . '.docx');
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            readfile($templateName);
            exit();
        }
        return $this->redirect($this->generateUrl('mgateSuivi_etude_homepage', array('page' => 1)));
    }


    private function array_push_assoc(&$array, $key, $value) {
        $array[$key] = $value;
        return $array;
    }


    //TODO
    private function getDoctypeAbsolutePathFromName($doc) {
        $em = $this->getDoctrine()->getManager();

        if (!$documenttype = $em->getRepository('mgate\PubliBundle\Entity\Document')->findOneBy(array('name' => $doc))) {
            throw $this->createNotFoundException('Le doctype n\'existe pas... C\'est bien balo');
        } else {
            $chemin = $documenttype->getWebPath();
        }
        return $chemin;
    }

    //Prendre tous les fichiers dans word
    private function getDocxContent($docxFullPath) {
        $zip = new \ZipArchive;
        $templateXML = array();
        if ($zip->open($docxFullPath) === TRUE) {


            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ((strstr($name, "document") || strstr($name, "header") || strstr($name, "footer")) && !strstr($name, "rels")) {
                    $this->array_push_assoc($templateXML, str_replace("word/", "", $name), $zip->getFromIndex($i));
                }
            }
            $zip->close();
        }
        return $templateXML;
    }

    private function traiterTemplates($templateFullPath, $rootName, $rootObject) {
        $templatesXML = $this->getDocxContent($templateFullPath); //récup contenu XML
        $templatesXMLTraite = array();

        foreach ($templatesXML as $templateName => $templateXML) {
            $templateXML = $this->get('twig')->render($templateXML, array($rootName => $rootObject));
            $this->array_push_assoc($templatesXMLTraite, $templateName, $templateXML);
        }

        return $templatesXMLTraite;
    }

    private function traiterImages(&$templatesXML, $images) {
        $allmatches = array();
        foreach ($templatesXML as $key => $templateXML) {
            $i = preg_match_all('#<!--IMAGE\|(.*?)\|\/IMAGE-->#', $templateXML, $matches);
            while ($i--) {
                $splited = preg_split("#\|#", $matches[1][$i]);
                if (isset($images[$splited[0]])) {
                    if (preg_match("#VAR#", $splited[0])) {
                        $cx = $splited[3];
                        $cy = $images[$splited[0]]['height'] * $cx / $images[$splited[0]]['width'];

                        $cx = round($cx);
                        $cy = round($cy);

                        $replacement = array();
                        preg_match("#wp:extent cx=\"$splited[3]\" cy=\"$splited[4]\".*wp:docPr.*a:blip r:embed=\"$splited[1]\".*a:ext cx=\"$splited[3]\" cy=\"$splited[4]\"#", $templateXML, $replacement);
                        $replacement = $replacement[0];
                        $replacement = preg_replace("#cy=\"$splited[4]\"#", "cy=\"$cy\"", $replacement);
                        $templatesXML[$key] = preg_replace("#wp:extent cx=\"$splited[3]\" cy=\"$splited[4]\".*wp:docPr.*a:blip r:embed=\"$splited[1]\".*a:ext cx=\"$splited[3]\" cy=\"$splited[4]\"#", $replacement, $templateXML);
                    }
                }
                array_push($allmatches, $splited);
            }
        }
        return $allmatches;
    }


    //Nettoie le dossier tmp : efface les fichiers temporaires vieux de plus de 1 jours
    private function purge() {
        $Patern = '*';
        $oldSec = 86400; // = 1 Jours
        $path = 'tmp/';
        clearstatcache();
        foreach (@glob($path . $Patern) as $filename) {
            if (filemtime($filename) + $oldSec < time())
                @unlink($filename);
        }
    }

    /**
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * //UNSAFE
     * //TODO
     * //PASPROPRE
     */
    public function verifierTemplatesAction(){
        $data = array();
        $form = $this->createFormBuilder($data)
            ->add('template', 'textarea')
            ->add('etudiant', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Intervenant',
               'required' => false
               ))
            ->add('etude','genemu_jqueryselect2_entity',array (
                      'label' => 'Etude',
                       'class' => 'mgate\\SuiviBundle\\Entity\\Etude',
                       'property' => 'reference',                      
                       'required' => false))
            ->getForm();

         if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {           
            
            $data = $form->getData();
            
            $template =  $data['template'];
            
            $template = preg_replace('#\{%\s*Pfor[^%]*%\}#', '$0<p>', $template);
            $template = preg_replace('#Pfor#', 'for', $template);
            $template = preg_replace('#\{%\s*endforP[^%]*%\}#', '$0</p>', $template);
            $template = preg_replace('#endforP#', 'endfor', $template);
            
            $template = preg_replace('#\{%\s*TRfor[^%]*%\}#', '$0<p>', $template);
            $template = preg_replace('#TRfor#', 'for', $template);
            $template = preg_replace('#\{%\s*endforTR[^%]*%\}#', '$0</p>', $template);
            $template = preg_replace('#endforTR#', 'endfor', $template);
            
            $template = preg_replace('#‘|’#','\'',$template);
            $template = preg_replace('#\n#','<br>',$template);
            
            return new \Symfony\Component\HttpFoundation\Response($this->get('twig')->render($template, array('etude'=> $data['etude'], 'etudiant' => $data['etudiant']))); 
            }
         }
        
        return new \Symfony\Component\HttpFoundation\Response(
            $this->get('twig')->render(
                '<form method="post" {{ form_enctype(form) }}>{{ form_widget(form) }}<input type="submit" value="Test"/></form>', 
                array('form' => $form->createView()))
            ); 
               
    }
}
