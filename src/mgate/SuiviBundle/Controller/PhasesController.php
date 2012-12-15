<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;
use mgate\SuiviBundle\Form\PhasesType;


class PhasesController extends Controller
{
    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }

    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        $originalPhases = array();
        // Create an array of the current Phase objects in the database
        foreach ($etude->getPhases() as $phase) {
            $originalPhases[] = $phase;
        }
        

        $form = $this->createForm(new PhasesType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                // filter $originalPhases to contain phases no longer present
                foreach ($etude->getPhases() as $phase) {
                    foreach ($originalPhases as $key => $toDel) {
                        if ($toDel->getId() === $phase->getId()) {
                            unset($originalPhases[$key]);
                        }
                    }
                }
                
                // remove the relationship between the phase and the etude
                foreach ($originalPhases as $phase) {
                    $em->remove($phase); // on peut faire un persist sinon, cf doc collection form
                }
                
                               
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                
                //Necessaire pour refraichir l ordre
                $em->refresh($etude);
                $form = $this->createForm(new PhasesType, $etude);
                //return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );

            }
        }
        
        return $this->render('mgateSuiviBundle:Phase:phases.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}
