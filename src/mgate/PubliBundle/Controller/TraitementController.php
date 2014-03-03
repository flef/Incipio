<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

class TraitementController extends Controller {

    private $SFD = '~';                                                         //Start Field       Delimiter
    private $EFD = '~~';                                                        //End   Field       Delimiter

    
    //Vérification du fichier
    //if match % _ % then pasbien
    private function verifierTemplates($templatesXML) {
        $SFD = $this->SFD;
        $EFD = $this->EFD;
        $allmatches = array();

        foreach ($templatesXML as $templateXML) {
            preg_match_all('#' . $SFD . '(.*?)' . $EFD . '#', $templateXML, $matches);
            $allmatches += $matches[1];
        }
        return $allmatches;
    }



    private function array_push_assoc(&$array, $key, $value) {
        $array[$key] = $value;
        return $array;
    }

    private function getEtudeFromID($id_etude) {
        $em = $this->getDoctrine()->getManager();

        //Récupère l'étude avec son id
        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude))
            throw $this->createNotFoundException('Etude[id=' . $id_etude . '] inexistant');
        
        return $etude;
    }

    private function getDoctypeAbsolutePathFromName($doc) {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if (!$documenttype = $em->getRepository('mgate\PubliBundle\Entity\Document')->findOneBy(array('name' => $doc))) {
            $chemin = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/bundles/mgatepubli/document-type/' . $doc . '.docx'; //asset
        } else {
            $chemin = $documenttype->getWebPath(); // on prend le document type qui est uploadé
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

    private function publipostageEtude($id_etude, $doc, $key) {
        $key = intval($key);

        $etude = $this->getEtudeFromID($id_etude);
        $chemin = $this->getDoctypeAbsolutePathFromName($doc);

        //DEBUG   
        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path . $doc . '.docx';
        }
        
        $templatesXMLtraite = $this->traiterTemplates($chemin, 'etude', $etude);
        $champsBrut = $this->verifierTemplates($templatesXMLtraite);
        $repertoire = 'tmp';

        //SI DM on prend la ref de RM et ont remplace RM par DM
        if ($doc == 'DM') {
            $doc = 'RM';  $isDM = true;
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

        
        /*
         * TRAITEMENT INSERT IMAGE
         */
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
        /*****/

        foreach ($templatesXMLtraite as $templateXMLName => $templateXMLContent) {
            $zip->deleteName('word/' . $templateXMLName);
            $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
        }
        
        $zip->close();

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;

        return array($champsBrut);
    }

    public function publiposterMultipleEtude($id_etude, $doc) {
        $etude = $this->getEtudeFromID($id_etude);
        $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc);
        $idZip = 'ZIP' . $refDocx . '-' . ((int) strtotime("now") + rand());
        $_SESSION['idZip'] = $idZip;

        foreach ($etude->getMissions() as $key => $mission) {
            $this->publipostageEtude($id_etude, $doc, $key);
            $this->telechargerAction('', true);
        }

        $_SESSION['refDocx'] = $refDocx;

        $this->telechargerAction('', false, true);
    }

    /** publication du doc
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function publiposterEtudeAction($id_etude, $doc, $key) {
        if (($doc == 'RM' || $doc == 'DM') && $key == -1)
            $champsBrut = $this->publiposterMultipleEtude($id_etude, $doc);
        else
            $champsBrut = $this->publipostageEtude($id_etude, $doc, $key);
        
        // TODO REACTIVER LES ERREURS
        if (false)//count($champsBrut[0])) {
            return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('nbreChampsNonRemplis' => count($champsBrut[0]), 'champsNonRemplis' => array_unique($champsBrut[0])));
        else
            return $this->telechargerAction($doc);
    }

    /** A nettoyer !!
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function telechargerAction($docType = 'AP', $addZip = false, $dlZip = false) {
        $junior = $this->container->getParameter('junior');
        $this->purge();
        if (isset($_SESSION['idDocx']) && isset($_SESSION['refDocx'])) {
            $idDocx = $_SESSION['idDocx'];
            $refDocx = $_SESSION['refDocx'];

            if ($addZip) {
                $idZip = $_SESSION['idZip'];
                $zip = new \ZipArchive;
                $zip->open('tmp/' . $idZip, \ZipArchive::CREATE);
                $zip->addFile('tmp/' . $idDocx, $junior['tag'].$refDocx . '.docx');
                $zip->close();
            } elseif ($dlZip) {
                $idZip = $_SESSION['idZip'];
                $doc = 'tmp/' . $idZip;

                header('Content-Type: application/zip');
                header('Content-Length: ' . filesize($doc));
                header('Content-disposition: inline; filename=' . $junior['tag'].$refDocx . '.zip');
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');
                readfile($doc);
                exit();
            } else {
                $doc = 'tmp/' . $idDocx;

                header('Content-Type: application/msword');
                header('Content-Length: ' . filesize($doc));
                header('Content-disposition: attachment; filename=' .$junior['tag']. $refDocx . '.docx');
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');
                readfile($doc);
                exit();
            }
        } else {
            
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_homepage', array('page' => 1)));
    }

    //Nettoie le dossier tmp : efface les fichiers temporaires vieux de plus de n = 1 jours
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

   

    /* Publipostage documents élèves
     *  
     */

    /** publication du doc
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function publiposterEleveAction($id_eleve, $doc, $key) {
        $champsBrut = $this->publipostageEleve($id_eleve, $doc, $key);

        // TODO REACTIVER ERREUR
        if (false)//count($champsBrut[0]))
            return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('nbreChampsNonRemplis' => count($champsBrut[0]), 'champsNonRemplis' => array_unique($champsBrut[0], SORT_STRING), 'aides' => $champsBrut[1]));
        else 
            return $this->telechargerAction($doc);
    }
    
    
    /***
     * 
     * OLD 
     * A CONVERTIR EN FILTRE
     * 
     * 
     */
     //Formate un nombre à la francaise ex 1 234,56 avec deux décimals 
    //SEE_ALSO moneyformat LC_MONETARY fr_FR
    private function formaterNombre($number) {
        if (is_int($number)) // Si entier
            return number_format($number, 0, ',', ' ');
        else
            return number_format($number, 2, ',', ' ');
    }

    private function jourVersSemaine($j) {
        $converter = $this->get('mgate.conversionlettre');

        $jour = $j % 7;
        $semaine = (int) floor($j / 7);

        $jour_str = $converter->ConvNumberLetter($jour);
        $semaine_str = $converter->ConvNumberLetter($semaine);

        $jourVersSemaine = "";
        if ($semaine)
            $jourVersSemaine = $semaine_str . ($semaine > 1 ? " semaines" : "e semaine");
        if ($jour)
            $jourVersSemaine .= ($semaine > 0 ? " et " : "" ) . $jour_str . " jour" . ($jour > 1 ? "s" : "");
        return $jourVersSemaine;
    }
    
    public function commenceParUneVoyelle($mot) {
        return preg_match('#^[aeiouy]#', $mot);
    }

    private function nombreVersMois($m) {
        $mois = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        return $mois[($m % 12) - 1];
    }
    /**
       //Remplissage des %champs%
      private function remplirChamps(&$templateXML, $fieldValues) {
          $SFD = $this->SFD;
 -
          $EFD = $this->EFD;
 +        
 +        $date = new \DateTime('now');
 +        $date = $date->format('Y-m-d\TH:i:sZ');
 +        
 +        $EID = '</w:t></w:ins>';
 +        
  
          foreach ($fieldValues as $field => $values) {//Remplacement des champs
 +            $SID = '<w:ins w:id="0" w:author="'.$field.'" w:date="'.$date.'"><w:t xml:space="preserve">';
              //WARNING : ($values != NULL) remplacer par ($values !== NULL), les valeurs NULL de type non NULL sont permises !!!!!!!
              //TODO : Verification type NULL sur champs vide 
              if ($values !== NULL) {
                  if (is_int($values) || is_float($values)) //Formatage des nombres à la francaise
                      $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', preg_replace("# #", " ", $this->formaterNombre($values)), $templateXML);
                  else
 -                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', $this->nl2wbr(htmlspecialchars($values)), $templateXML);
 +                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', ' '.$SID.$this->nl2wbr(htmlspecialchars($values)).$EID, $templateXML);
              }
          }
     * 
     */

}
