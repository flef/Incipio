<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PersonneBundle\Entity\Prospect;
use mgate\PersonneBundle\Form\ProspectType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ProspectController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */   
    public function ajouterAction($format)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $prospect = new Prospect;
        
        $form = $this->createForm(new ProspectType, $prospect);   
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($prospect);    
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgatePersonne_prospect_voir', array('id' => $prospect->getId())) );
            }
        }
        


        return $this->render('mgatePersonneBundle:Prospect:ajouter.html.twig', array(
            'form' => $form->createView(),
            'format' => $format
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Prospect')->findAll();

        return $this->render('mgatePersonneBundle:Prospect:index.html.twig', array(
            'prospects' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */       
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Prospect')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        //$deleteForm = $this->createDeleteForm($id); // cf TestBundle

        return $this->render('mgatePersonneBundle:Prospect:voir.html.twig', array(
            'prospect'      => $entity,
            /*'delete_form' => $deleteForm->createView(),*/ ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */       
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $prospect = $em->getRepository('mgate\PersonneBundle\Entity\Prospect')->find($id) )
        {
            throw $this->createNotFoundException('Prospect[id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new ProspectType, $prospect);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($prospect);    
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgatePersonne_prospect_voir', array('id' => $prospect->getId())) );

            }
        }

        return $this->render('mgatePersonneBundle:Prospect:modifier.html.twig', array(
            'form' => $form->createView(),
            'prospect'      => $prospect,
        ));
    }
    
 
    /**
     * @Route("/ajax_prospect", name="ajax_prospect")
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function ajaxProspectAction(Request $request)
    {
      
        $value = $request->get('term');

        $em = $this->getDoctrine()->getEntityManager();
        $members = $em->getRepository('mgatePersonneBundle:Prospect')->ajaxSearch($value);

        $json = array();
        foreach ($members as $member) {
            $json[] = array(
                'label' => $member->getNom(),
                'value' => $member->getId()
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));
        
        
        return $response;
    }  
}
