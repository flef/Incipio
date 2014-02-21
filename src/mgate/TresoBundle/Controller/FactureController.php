<?php

namespace mgate\TresoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PubliBundle\Controller\ConversionLettreController as Formater;
use \mgate\TresoBundle\Entity\Facture as Facture;
use \mgate\TresoBundle\Entity\FactureDetail as FactureDetail;
use mgate\TresoBundle\Form\FactureType as FactureType;

class FactureController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('mgateTresoBundle:Facture')->findAll();
        
        return $this->render('mgateTresoBundle:Facture:index.html.twig', array('factures' => $factures));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();
        if(!$facture = $em->getRepository('mgateTresoBundle:Facture')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');
        
        return $this->render('mgateTresoBundle:Facture:voir.html.twig', array('facture' => $facture));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id, $etude_id) {
        $em = $this->getDoctrine()->getManager();
        $tauxTVA = 20.0;
        if (!$facture= $em->getRepository('mgateTresoBundle:Facture')->find($id)) {
            $facture = new Facture;
            $now = new \DateTime("now");
            $facture->setDateEmission($now);
            
            if( $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($etude_id)){             
                if($etude->getCc())
                    $refCC = $etude->getCc()->getReference();
                $formater = new Formater;
                
                
                $facture->setEtude($etude);
                $facture->setBeneficiaire($etude->getProspect());            
                
                if(!count($etude->getFactures()) && $etude->getAcompte()){
                    $facture->setType(Facture::$TYPE_VENTE_ACCOMPTE);
                    
                    $montantTTC = $etude->getPourcentageAcompte() * $etude->getMontantHT() * $tauxTVA / 100;
                    $montantTTCLettre =  $formater->ConvNumberLetter($montantTTC,1); 
                    $facture->setObjet('Conformément à la convention client '.$refCC.', nous vous prions de nous verser la somme de '. $formater->money_format($montantTTC).' € T.T.C. ('.$montantTTCLettre.' T.T.C), correspondant au règlement de '.$formater->money_format(($etude->getPourcentageAcompte() * 100)).' % de l’étude.');

                    $detail = new FactureDetail;
                    $detail->setCompte();
                    $detail->setFacture($facture);
                    $facture->addDetail($detail);
                    $detail->setDescription('Acompte de '. $formater->money_format(($etude->getPourcentageAcompte() * 100)).' % sur l\'étude '.$etude->getReference());                    
                    $detail->setMontantHT($etude->getPourcentageAcompte() * $etude->getMontantHT());
                    $detail->setTauxTVA($tauxTVA);
                }
                else{
                    $facture->setType(Facture::$TYPE_VENTE_SOLDE);
                    
                    $totalTTC = 0;
                    foreach ($etude->getPhases() as $phase){
                        $detail = new FactureDetail;
                        $detail->setCompte();
                        $detail->setFacture($facture);
                        $facture->addDetail($detail);
                        $detail->setDescription('Phase '.($phase->getPosition() + 1). ' : '.$phase->getTitre().' : ' . $phase->getNbrJEH() . ' JEH * '. $formater->money_format($phase->getPrixJEH()) . ' €');                    
                        $detail->setMontantHT($phase->getPrixJEH() * $phase->getNbrJEH());
                        $detail->setTauxTVA($tauxTVA);
                        
                        $totalTTC += $phase->getPrixJEH() * $phase->getNbrJEH();                        
                    }
                    $detail = new FactureDetail;
                    $detail->setCompte()
                           ->setFacture($facture)
                           ->setDescription('Frais de dossier')
                           ->setMontantHT($etude->getFraisDossier());
                    $facture->addDetail($detail);
                    $detail->setTauxTVA($tauxTVA);
                    
                    $totalTTC += $etude->getFraisDossier();
                    $totalTTC *= (1 + $tauxTVA / 100);
                    $totalTTCLettre =  $formater->ConvNumberLetter($totalTTC,1); 
                    
                    $facture->setObjet('Conformément à la convention client '.$refCC.', nous vous prions de nous verser la somme de '. $formater->money_format($totalTTC).' € T.T.C. ('.$totalTTCLettre.' T.T.C), correspondant au règlement de correspondant au solde de l’étude.'. ($etude->getFa() ? 'La facture d’acompte '.$etude->getFa()->getReference().' a été prise en compte.' : ''));
                    
                }            
            }
        }

        $form = $this->createForm(new FactureType, $facture);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                foreach($facture->getDetails() as $factured){
                    $factured->setFacture($facture);
                }

                if($facture->getType() <= Facture::$TYPE_VENTE_ACCOMPTE || $facture->getMontantADeduire() == null || $facture->getMontantADeduire()->getMontantHT() == 0)
                    $facture->setMontantADeduire(null);
                $em->persist($facture);                
                $em->flush();
                return $this->redirect($this->generateUrl('mgateTreso_Facture_voir', array('id' => $facture->getId())));
            }
        }

        return $this->render('mgateTresoBundle:Facture:modifier.html.twig', array(
                    'form' => $form->createView(),
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$facture= $em->getRepository('mgateTresoBundle:Facture')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');
        
        foreach ($facture->getDetails() as $detail)
            $em->remove($detail);
        $em->flush();

        $em->remove($facture);
        $em->flush();
        
        return $this->redirect($this->generateUrl('mgateTreso_Facture_index', array()));


    }
}
