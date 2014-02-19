<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\FactureVente;
use mgate\SuiviBundle\Form\FactureVenteType;
use mgate\SuiviBundle\Form\FactureVenteSubType;


class FactureVenteController extends Controller
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

        $entity = $em->getRepository('mgateSuiviBundle:FactureVente')->find($id); // Ligne qui posse problÃ¨me

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FactureVente entity.');
        }
		
		$etude = $entity->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:FactureVente:voir.html.twig', array(
            'FactureVente'      => $entity,
            'etude'      => $entity->getEtude(),
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
 
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) ) {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        
        $FactureVente = new FactureVente;
        $etude->addFi($FactureVente);
        $FactureVente->setNum($this->get('mgate.etude_manager')->getNouveauNumeroFactureVente());
        
        $time = time();
        $now = new \DateTime("@$time");
        $FactureVente->setDateSignature($now);
        
        $form = $this->createForm(new FactureVenteSubType, $FactureVente, array('type' => 'fi'));   
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
       
            if( $form->isValid() )
            {
                //Vérification du montant de la FactureVente
                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    foreach($etude->getFis() as $fi)
                        $montantHT -= $fi->getMontantHT();
                    if($etude->getFs())
                        $montantHT -= $etude->getFs()->getMontantHT();
                    
                    $montantHT -= $form->get('montantHT')->getData();
                    
                    if($montantHT < 0)
                    {
                        throw new \Exception('Montant impossible, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));
                    }
                    
                    //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($FactureVente);
                $FactureVente->setExercice($exercice);
                    
                $em->persist($FactureVente);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgateSuivi_FactureVente_voir', array('id' => $FactureVente->getId())) );
            }
            
        }

        return $this->render('mgateSuiviBundle:FactureVente:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id_FactureVente)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $FactureVente = $em->getRepository('mgate\SuiviBundle\Entity\FactureVente')->find($id_FactureVente) )
            throw $this->createNotFoundException('FactureVente[id='.$id_FactureVente.'] inexistant');
			
		$etude = $FactureVente->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $form = $this->createForm(new FactureVenteSubType, $FactureVente, array('type' => $FactureVente->getType()));
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                //vérification montant FactureVente
                    $etude = $FactureVente->getEtude();
                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    foreach($etude->getFis() as $fi)
                        $montantHT -= $fi->getMontantHT();
                    if($etude->getFs())
                        $montantHT -= $etude->getFs()->getMontantHT();
                                        
                    $montantHT += $FactureVente->getMontantHT();
                    $montantHT -= $form->get('montantHT')->getData();
                    
                    if($montantHT < 0)
                    {
                        $montantHT += $form->get('montantHT')->getData();
                        throw new \Exception('Montant impossible, le client doit encore : ' . $montantHT. ' €');
                    }
                    ///
                    
                    //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($FactureVente);
                $FactureVente->setExercice($exercice);
                    
                $em->persist($FactureVente);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_FactureVente_voir', array('id' => $FactureVente->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:FactureVente:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $FactureVente->getEtude(),
            'type' => $FactureVente->getType(),
            'FactureVente' => $FactureVente,
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
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        if(!$FactureVente = $etude->getDoc($type))
        {
            $FactureVente = new FactureVente;
            
            $time = time();
            $now = new \DateTime("@$time");
            $FactureVente->setDateSignature($now);
        
        
            if(strtoupper($type)=="FA")
            {
                $etude->setFa($FactureVente);
                $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
            }
            elseif(strtoupper($type)=="FS"){
                $etude->setFs($FactureVente);
            }  
            $FactureVente->setType($type);
            $FactureVente->setNum($this->get('mgate.etude_manager')->getNouveauNumeroFactureVente());
        }
        
        if(strtoupper($type)=="FS"){
                $etude->setFs($FactureVente);
                
                $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                if($etude->getFa())
                    $montantHT -= $etude->getFa()->getMontantHT();
                foreach($etude->getFis() as $fi)
                    $montantHT -= $fi->getMontantHT();
                
                if($montantHT < 0)
                    throw new \Exception('Montant impossible, vérifier les FactureVentes intermédiaires, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));

                
                $etude->getFs()->setMontantHT($montantHT);
            }  

        $form = $this->createForm(new FactureVenteType, $etude, array('type' => $type));
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                if(strtoupper($type)=="FA")
                    $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
                elseif(strtoupper($type)=="FS"){ 
                    $etude->setFs($FactureVente);

                    $montantHT = $this->get('mgate.etude_manager')->getTotalHT($etude);
                    if($etude->getFa())
                        $montantHT -= $etude->getFa()->getMontantHT();
                    foreach($etude->getFis() as $fi)
                        $montantHT -= $fi->getMontantHT();

                    if($montantHT < 0)
                        throw new \Exception('Montant impossible, vérifier les FactureVentes intermédiaires, le client doit encore : ' . ($montantHT + $form->get('montantHT')->getData() . ' €'));

                    $etude->getFs()->setMontantHT($montantHT);
                } 
                                
                //Exercice comptable
                $exercice = $this->get('mgate.etude_manager')->getExerciceComptable($FactureVente);
                $FactureVente->setExercice($exercice);
                                        
                $em->persist($etude);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_FactureVente_voir', array('id' => $FactureVente->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:FactureVente:rediger.html.twig', array(
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
   
            if( ! $entity = $em->getRepository('mgate\SuiviBundle\Entity\FactureVente')->find($id) )
                throw $this->createNotFoundException('FactureVente[id='.$id.'] inexistant');
				
			$etude = $entity->getEtude();
		
			if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
				throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

            $em->remove($entity);
            $em->flush();
            
        return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $entity->getNumero())));
    }
   
}
