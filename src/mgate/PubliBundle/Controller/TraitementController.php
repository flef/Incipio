<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;



class TraitementController extends Controller {
    
    const DOCTYPE_SUIVI_ETUDE               = 'FSE';
    const DOCTYPE_AVANT_PROJET              = 'AP';
    const DOCTYPE_CONVENTION_CLIENT         = 'CC';
    const DOCTYPE_FACTURE_ACOMTE            = 'FA';
    const DOCTYPE_FACTURE_INTERMEDIAIRE     = 'FI';
    const DOCTYPE_FACTURE_SOLDE             = 'FS';
    const DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE = 'PVI';
    const DOCTYPE_PROCES_VERBAL_FINAL       = 'PVR';
    const DOCTYPE_RECAPITULATIF_MISSION     = 'RM';
    const DOCTYPE_DESCRIPTIF_MISSION        = 'DM';
    const DOCTYPE_CONVENTION_ETUDIANT       = 'CE';
    const DOCTYPE_FICHE_ADHESION            = 'FM';
    const DOCTYPE_ACCORD_CONFIDENTIALITE    = 'AC';
    const DOCTYPE_DECLARATION_ETUDIANT_ETR  = 'DEE';
    
    // On considère que les TAG ont déjà été nettoyé du XML
    const REG_REPEAT_LINE       = "#(<w:tr(?:(?!w:tr\s).)*?)(\{\%\s*TRfor[^\%]*\%\})(.*?)(\{\%\s*endforTR\s*\%\})(.*?</w:tr>)#";
    const REG_REPEAT_PARAGRAPH  = "#(<w:p(?:(?!<w:p\s).)*?)(\{\%\s*Pfor[^\%]*\%\})(.*?)(\{\%\s*endforP\s*\%\})(.*?</w:p>)#";
    // Champs
    const REG_CHECK_FIELDS = "#\{[\{%].*?[\}%]\}#";
    const REG_XML_NODE_IDENTIFICATOR = "#<.*?>#";
    // Images
    const REG_IMAGE_DOC = "#<w:drawing.*?/w:drawing>#";
    const REG_IMAGE_DOC_FIELD = "#wp:extent cx=\"(\\d+)\" cy=\"(\\d+)\".*wp:docPr.*descr=\"(.*?)\".*a:blip r:embed=\"(rId\\d+)#";
    const REG_IMAGE_REL = "#Id=\"(rId\\d+)\" Type=\"\\S*\" Target=\"media\\/(image\\d+.(jpeg|jpg|png))\"#";
    const IMAGE_FIX = "#imageFIX#";
    const IMAGE_VAR = "#imageVAR#";
    // Autres
    const REG_SPECIAL_CHAR = '{}()[]|';
    const REG_FILE_EXT = "#\.(jpg|png|jpeg)#i";

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
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                if($this->get('mgate.etude_manager')->confidentielRefus($rootObject, $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            case 'etudiant':
                if (!$rootObject = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($rootObject_id))
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                break;
            case 'mission':
                if (!$rootObject = $em->getRepository('mgate\SuiviBundle\Entity\Mission')->find($rootObject_id))
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                break;
            case 'facture':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\Facture')->find($rootObject_id))
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                if($rootObject->getEtude() && $this->get('mgate.etude_manager')->confidentielRefus($rootObject->getEtude(), $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            case 'nf':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\NoteDeFrais')->find($rootObject_id))
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                break;
            case 'bv':
                if (!$rootObject = $em->getRepository('mgate\TresoBundle\Entity\BV')->find($rootObject_id))
                    throw $this->createNotFoundException('Le document ne peut être plubliposter car l\'objet de référence n\'existe pas !');
                if($rootObject->getMission() && $rootObject->getMission()->getEtude() && $this->get('mgate.etude_manager')->confidentielRefus($rootObject->getMission()->getEtude(), $this->container->get('security.context')))
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
                break;
            default:
                throw $this->createNotFoundException('Publipostage invalide ! Pas de bol...');
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
        if ($templateName == self::DOCTYPE_DESCRIPTIF_MISSION) {
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

        $zip = new \ZipArchive();
        $zip->open($repertoire . '/' . $idDocx);
        
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
    
    //prendre le fichier relationShip
    private function getDocxRelationShip($docxFullPath){
        $zip = new \ZipArchive;
        $templateXML = array();
        if ($zip->open($docxFullPath) === TRUE) {


            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ((strstr($name, "document.xml.rel")))
                    $templateXML = $zip->getFromIndex($i);
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
     * Traitement des champs (Nettoyage XML)
     */
    private function cleanDocxFields(&$templateXML){
        $fields = array();
        preg_match_all(self::REG_CHECK_FIELDS, $templateXML, $fields);
        $fields = $fields[0];
        foreach($fields as $field){
            $originalField = $field;                
            $field = preg_replace('#‘#', '\'', $field); // Peut etre simplifier en une ligne avec un array
            $field = preg_replace('#’#', '\'', $field);
            $field = preg_replace(self::REG_XML_NODE_IDENTIFICATOR, '', $field);
            if($field == strtoupper($field))
                $field = strtolower($field);

            $templateXML = preg_replace('#'. addcslashes(addslashes($originalField), self::REG_SPECIAL_CHAR) .'#', $field, $templateXML);   
        } 
        return $templateXML;
    }
    
    /**
    * Traitement des lignes de tableaux
    */
    private function cleanDocxTableRow(&$templateXML){    
       $parts = array();
       $nbr = preg_match_all(self::REG_REPEAT_LINE, $templateXML, $parts);
       $datas = array();
       foreach ($parts as $part){
           for($i = 0; $i < $nbr; $i++){
               $datas[$i][] = $part[$i];
           }
       }

       foreach ($datas as $data){
           $forStart = $data[2];               
           $forEnd = $data[4];

           $body = preg_replace(array(
               '#'. addcslashes(addslashes($forStart), self::REG_SPECIAL_CHAR) .'#',
               '#'. addcslashes(addslashes($forEnd), self::REG_SPECIAL_CHAR) .'#'), '', $data[0]);

           $templateXML = preg_replace('#'. addcslashes(addslashes($data[0]), self::REG_SPECIAL_CHAR) .'#', preg_replace('#TRfor#', 'for', $forStart).$body.'{% endfor %}', $templateXML);
       }
       
       return $templateXML;
    }
    
    /**
    * Traitement Paragraphe
    */
    private function cleanDocxParagraph(&$templateXML){
       $parts = array();
       $nbr = preg_match_all(self::REG_REPEAT_PARAGRAPH, $templateXML, $parts);
       $datas = array();
       foreach ($parts as $part){
           for($i = 0; $i < $nbr; $i++){
               $datas[$i][] = $part[$i];
           }
       }

       foreach ($datas as $data){
           $forStart = $data[2];               
           $forEnd = $data[4];

           $body = preg_replace(array(
               '#'. addcslashes(addslashes($forStart), self::REG_SPECIAL_CHAR) .'#',
               '#'. addcslashes(addslashes($forEnd), self::REG_SPECIAL_CHAR) .'#'), '', $data[0]);

           $templateXML = preg_replace('#'. addcslashes(addslashes($data[0]), self::REG_SPECIAL_CHAR) .'#', preg_replace('#Pfor#', 'for', $forStart).$body.'{% endfor %}', $templateXML);
       }
       
       return $templateXML;
    }
    
    /**
     * Traitement des images
     */
    private function linkDocxImages(&$templateXML, $relationship){
        $images = array();
        preg_match(self::REG_IMAGE_DOC, $templateXML, $images);
        foreach ($images as $image){
            $imageInfo = array();
            if(preg_match(self::REG_IMAGE_DOC_FIELD, $image, $imageInfo)){
                $cx = $imageInfo[1];
                $cy = $imageInfo[2];
                $fileName = explode('\\', $imageInfo[3]);
                $originalFilename = preg_replace(self::REG_FILE_EXT,  '', end($fileName));                    
                $rId = $imageInfo[4];

                if(preg_match(self::IMAGE_VAR, $originalFilename) || preg_match(self::IMAGE_VAR, $originalFilename)){
                    $relatedImage = array();
                    preg_match(self::REG_IMAGE_REL, $relationship, $relatedImage);
                    $localFilename = $relatedImage[2];

                    $commentsRel = "<!--IMAGE|" . $originalFilename . "|" . $rId . "|" . $localFilename . "|" . $cx . "|" . $cy . "|/IMAGE-->";
                    $templateXML = preg_replace("#(<\?.*?\?>)#", "$0$commentsRel", $templateXML, 1);
                }
            }
        }
        return $templateXML;
    }


    /**
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function uploadNewDoctypeAction(){
        $message = '';
        $data = array();
        $form = $this->createFormBuilder($data)
            ->add('name', 'choice', array(
                'required' => true, 
                'choices' => array(
                    self::DOCTYPE_SUIVI_ETUDE => 'Fiche de suivi d\'étude',
                    self::DOCTYPE_AVANT_PROJET => 'Avant-Projet',
                    self::DOCTYPE_CONVENTION_CLIENT => 'Convention Client',
                    self::DOCTYPE_FACTURE_ACOMTE => 'Facture d\'acompte',
                    self::DOCTYPE_FACTURE_INTERMEDIAIRE => 'Facture intermédiaire',
                    self::DOCTYPE_FACTURE_SOLDE => 'Facture de solde',
                    self::DOCTYPE_PROCES_VERBAL_INTERMEDIAIRE => 'Procès verbal de recette intermédiaire',
                    self::DOCTYPE_PROCES_VERBAL_FINAL => 'Procès verbal de recette final',
                    self::DOCTYPE_RECAPITULATIF_MISSION => 'Récapitulatif de mission',
                    self::DOCTYPE_DESCRIPTIF_MISSION => 'Descriptif de mission',
                    self::DOCTYPE_CONVENTION_ETUDIANT => 'Convention Etudiant',
                    self::DOCTYPE_FICHE_ADHESION => 'Fiche d\'adhésion',
                    self::DOCTYPE_ACCORD_CONFIDENTIALITE => 'Accord de confidentialité',
                    self::DOCTYPE_DECLARATION_ETUDIANT_ETR => 'Déclaration étudiant étranger',
                    )))
            /*->add('etudiant', 'genemu_jqueryselect2_entity', array(
               'class' => 'mgate\\PersonneBundle\\Entity\\Membre',
               'property' => 'personne.prenomNom',
               'label' => 'Intervenant pour vérifier le template',
               'required' => false
               ))*/
            ->add('template', 'file', array('required' => true))
            /*->add('etude','genemu_jqueryselect2_entity',array (
                      'label' => 'Etude pour vérifier le template',
                       'class' => 'mgate\\SuiviBundle\\Entity\\Etude',
                       'property' => 'reference',                      
                       'required' => false))
            ->add('verification', 'checkbox', array('label' => 'Désactiver la vérification', 'required' => false))*/
            ->getForm();

         if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {           
                $data = $form->getData();

                // Création d'un fichier temporaire
                $file = $data['template'];
                $filename = sha1(uniqid(mt_rand(), true));
                $filename .= '.'.$file->guessExtension();
                $file->move('tmp/', $filename);
                $docxFullPath = 'tmp/'. $filename;

                // Extraction des infos XML
                $templatesXML = $this->getDocxContent($docxFullPath);
                $relationship = $this->getDocxRelationShip($docxFullPath);                
                // Nettoyage des XML
                $templatesXMLTraite = array(); 
                foreach ($templatesXML as $templateName => $templateXML) {
                    $this->cleanDocxFields($templateXML);
                    $this->cleanDocxTableRow($templateXML);
                    $this->cleanDocxParagraph($templateXML);
                    $this->linkDocxImages($templateXML, $relationship);
                    $this->array_push_assoc($templatesXMLTraite, $templateName, $templateXML);
                }
                
                // Enregistrement dans le fichier temporaire
                $zip = new \ZipArchive();
                $zip->open($docxFullPath);

                foreach ($templatesXMLTraite as $templateXMLName => $templateXMLContent) {
                    $zip->deleteName('word/' . $templateXMLName);
                    $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
                }
                $zip->close();
        
                // Vérification du template
                // TODO
                
                
                // Enregistrement du template
                $em = $this->getDoctrine()->getManager();
                $user = $this->get('security.context')->getToken()->getUser();
                $personne = $user->getPersonne();
                $file = new \Symfony\Component\HttpFoundation\File\File($docxFullPath);
                
                
                $doc = new \mgate\PubliBundle\Entity\Document();
                $doc->setAuthor($personne)
                    ->setName($data['name'])
                    ->setFile($file);
                $em->persist($doc);
                $docs = $em->getRepository('mgatePubliBundle:Document')->findBy(array('name' => $doc->getName() ));
                foreach ($docs as $doc) $em->remove($doc);
                $em->flush();
                
                $message = 'Le document a été mis à jour : ';
            }
         }

        return new \Symfony\Component\HttpFoundation\Response(
            $this->get('twig')->render(
                $message.(array_key_exists('name', $data) ? $data['name'] : '').'<br><form method="post" {{ form_enctype(form) }}>{{ form_widget(form) }}<input type="submit" value="Mettre à jour"/></form>', 
                array('form' => $form->createView()))
            ); 
               
    }
    
    
    
}
