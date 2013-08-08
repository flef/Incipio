<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Facture;
use mgate\SuiviBundle\Form\FactureType;
use mgate\SuiviBundle\Form\FactureSubType;


class FactureController extends Controller
{    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }  
               
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Facture')->find($id); // Ligne qui posse problÃ¨me

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Facture entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Facture:voir.html.twig', array(
            'facture'      => $entity,
            'etude'      => $entity->getEtude(),
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
 
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        
        $facture = new Facture;
        
        $etude->addFi($facture);
        
        $facture->setNum($this->get('mgate.etude_manager')->getNouveauNumeroFacture());
        
        $time = time();
        $now = new \DateTime("@$time");
        $facture->setDateSignature($now);
        
        $form = $this->createForm(new FactureSubType, $facture, array('type' => 'fi'));   
        

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
       
            if( $form->isValid() )
            {
                //Vérification du montant de la facture
                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    if($etude->getFis()){
                        foreach($etude->getFis() as $fi)
                            $montantHT -= $fi->getMontantHT();
                    }
                    if($etude->getFs())
                        $montantHT -= $etude->getFs()->getMontantHT();
                    
                    $montantHT -= $form->get('montantHT')->getData();
                    
                    if($montantHT < 0)
                    {
                        throw new \Exception('Montant impossible, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));
                    }
                    
                    //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($facture);
                $facture->setExercice($exercice);
                    
                $em->persist($facture);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
            
        }

        return $this->render('mgateSuiviBundle:Facture:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id_facture)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $facture = $em->getRepository('mgate\SuiviBundle\Entity\Facture')->find($id_facture) )
            throw $this->createNotFoundException('Facture[id='.$id_facture.'] inexistant');

        $form = $this->createForm(new FactureSubType, $facture, array('type' => $facture->getType()));
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                //vérification montant facture
                    $etude = $facture->getEtude();
                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    
                    if($etude->getFis()){
                        foreach($etude->getFis() as $fi)
                            $montantHT -= $fi->getMontantHT();
                    }
                    if($etude->getFs())
                        $montantHT -= $etude->getFs()->getMontantHT();
                                        
                    $montantHT += $facture->getMontantHT();
                    $montantHT -= $form->get('montantHT')->getData();
                    
                    if($montantHT < 0)
                    {
                        $montantHT += $form->get('montantHT')->getData();
                        throw new \Exception('Montant impossible, le client doit encore : ' . $montantHT. ' €');
                    }
                    ///
                    
                    //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($facture);
                $facture->setExercice($exercice);
                    
                $em->persist($facture);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Facture:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $facture->getEtude(),
            'type' => $facture->getType(),
            'facture' => $facture,
        ));
    }   
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id_etude, $type)
    {
        $erreur = null;
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude) )
        {
            throw $this->createNotFoundException('Etude[id='.$id_etude.'] inexistant');
        }

        if(!$facture = $etude->getDoc($type))
        {
            $facture = new Facture;
            
            $time = time();
            $now = new \DateTime("@$time");
            $facture->setDateSignature($now);
        
        
            if(strtoupper($type)=="FA")
            {
                $etude->setFa($facture);
                $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
            }
            elseif(strtoupper($type)=="FS"){
                $etude->setFs($facture);
            }  
            $facture->setType($type);
            $facture->setNum($this->get('mgate.etude_manager')->getNouveauNumeroFacture());
        }
        
        if(strtoupper($type)=="FS"){
                $etude->setFs($facture);
                
                $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                if($etude->getFa())
                    $montantHT -= $etude->getFa()->getMontantHT();
                if($etude->getFis()){
                    foreach($etude->getFis() as $fi)
                        $montantHT -= $fi->getMontantHT();
                }
                
                if($montantHT < 0)
                    throw new \Exception('Montant impossible, vérifier les factures intermédiaires, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));

                
                $etude->getFs()->setMontantHT($montantHT);
            }  

        $form = $this->createForm(new FactureType, $etude, array('type' => $type));
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                if(strtoupper($type)=="FA")
                    $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
                elseif(strtoupper($type)=="FS"){ 
                    $etude->setFs($facture);

                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    if($etude->getFis()){
                        foreach($etude->getFis() as $fi)
                            $montantHT -= $fi->getMontantHT();
                    }

                    if($montantHT < 0)
                        throw new \Exception('Montant impossible, vérifier les factures intermédiaires, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));

                    $etude->getFs()->setMontantHT($montantHT);
                } 
                                
                //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($facture);
                $facture->setExercice($exercice);
                                        
                $em->persist($etude);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Facture:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
            'type' => $type,
            'error' => $erreur,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
   
            if( ! $entity = $em->getRepository('mgate\SuiviBundle\Entity\Facture')->find($id) )
                throw $this->createNotFoundException('Facture[id='.$id.'] inexistant');

            $em->remove($entity);
            $em->flush();
            
        return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('id' => $entity->getEtude()->getId())));
    }
   
}
