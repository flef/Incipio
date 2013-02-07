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
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        
        $facture = new Facture;
        $etude->addFi($facture);
        
        $form = $this->createForm(new FactureSubType, $facture, array('type' => 'fi'));      
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
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
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $facture = $em->getRepository('mgate\SuiviBundle\Entity\Facture')->find($id) )
        {
            throw $this->createNotFoundException('Facture[id='.$id.'] inexistant');
        }
        
        return $this->redigerAction($facture->getEtude()->getId(), $facture->getType(), 0);

    }
    
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id, $type, $keyFi)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }

        if(!$facture = $etude->getDoc($type))
        {
            $facture = new Facture;
            if(strtoupper($type)=="FA")
            {
                $etude->setFa($facture);
                $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
            }
            elseif(strtoupper($type)=="FS")
                $etude->setFs($facture);

            $facture->setType($type);
        }

        $form = $this->createForm(new FactureType, $etude, array('type' => $type));
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
            
            if( $form->isValid() )
            {
                if(strtoupper($type)=="FA")
                    $etude->getFa()->setMontantHT($this->get('mgate.etude_manager')->getTotalHT($etude)*$etude->getPourcentageAcompte());
                
                $em->persist($etude);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Facture:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
            'type' => $type,
        ));
    }
}
