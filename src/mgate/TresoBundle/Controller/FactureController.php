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
        $compteEtude = 705000;
        $compteFrais =  708500;
        $compteAcompte = 419100;
        
        
        if (!$facture= $em->getRepository('mgateTresoBundle:Facture')->find($id)) {
            $facture = new Facture;
            $now = new \DateTime("now");
            $facture->setDateEmission($now);
            
            if( $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($etude_id)){             
                $formater = new Formater;
                
                
                $facture->setEtude($etude);
                $facture->setBeneficiaire($etude->getProspect());            
                
                if(!count($etude->getFactures()) && $etude->getAcompte()){
                    $facture->setType(Facture::TYPE_VENTE_ACCOMPTE);
                    $facture->setObjet('Facture d\'Acompte sur l\'étude '.$etude->getReference().', correspondant au règlement de '.$formater->money_format(($etude->getPourcentageAcompte() * 100)).' % de l’étude.');
                    $detail = new FactureDetail;
                    $detail->setCompte( $em->getRepository('mgateTresoBundle:Compte')->findOneBy(array('numero' => $compteAcompte)));
                    $detail->setFacture($facture);
                    $facture->addDetail($detail);
                    $detail->setDescription('Acompte de '. $formater->money_format(($etude->getPourcentageAcompte() * 100)).' % sur l\'étude '.$etude->getReference());                    
                    $detail->setMontantHT($etude->getPourcentageAcompte() * $etude->getMontantHT());
                    $detail->setTauxTVA($tauxTVA);
                }
                else{
                    $facture->setType(Facture::TYPE_VENTE_SOLDE);
                    
                    $totalTTC = 0;
                    foreach ($etude->getPhases() as $phase){
                        $detail = new FactureDetail;
                        $detail->setCompte($em->getRepository('mgateTresoBundle:Compte')->findOneBy(array('numero' => $compteEtude)));
                        $detail->setFacture($facture);
                        $facture->addDetail($detail);
                        $detail->setDescription('Phase '.($phase->getPosition() + 1). ' : '.$phase->getTitre().' : ' . $phase->getNbrJEH() . ' JEH * '. $formater->money_format($phase->getPrixJEH()) . ' €');                    
                        $detail->setMontantHT($phase->getPrixJEH() * $phase->getNbrJEH());
                        $detail->setTauxTVA($tauxTVA);
                        
                        $totalTTC += $phase->getPrixJEH() * $phase->getNbrJEH();                        
                    }
                    $detail = new FactureDetail;
                    $detail->setCompte($em->getRepository('mgateTresoBundle:Compte')->findOneBy(array('numero' => $compteFrais)))
                           ->setFacture($facture)
                           ->setDescription('Frais de dossier')
                           ->setMontantHT($etude->getFraisDossier());
                    $facture->addDetail($detail);
                    $detail->setTauxTVA($tauxTVA);
                    
                    $totalTTC += $etude->getFraisDossier();
                    $totalTTC *= (1 + $tauxTVA / 100);
                    $totalTTCLettre =  $formater->ConvNumberLetter($totalTTC,1); 
                    
                    $facture->setObjet('Facture de Solde sur l\'étude '.$etude->getReference().'.');
                    
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
