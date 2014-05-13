<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\DomaineCompetence;
use mgate\SuiviBundle\Form\DomaineCompetenceType;


class DomaineCompetenceController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $entities = $em->getRepository('mgateSuiviBundle:DomaineCompetence')->findAll();

        $domaine = new DomaineCompetence;

        $form = $this->createForm(new DomaineCompetenceType(), $domaine);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
            	$em->persist($domaine);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_domaine_index'));
            }
        }

        return $this->render('mgateSuiviBundle:DomaineCompetence:index.html.twig', array(
            'domaines' => $entities,
            'form' => $form->createView(),
        ));  
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();        
        

        if(!$domaine = $em->getRepository('mgate\SuiviBundle\Entity\DomaineCompetence')->find($id) )
            throw $this->createNotFoundException('Ce domaine de competence n\'existe pas !');
        
        $em->remove($domaine);
        $em->flush();

		return $this->redirect( $this->generateUrl('mgateSuivi_domaine_index'));
    }


}
