<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\ClientContactHandler;

use mgate\SuiviBundle\Entity\ClientContact;
use mgate\SuiviBundle\Form\ClientContactType;


class ClientContactController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:ClientContact')->findBy(array(), array('date' => 'ASC'));

        return $this->render('mgateSuiviBundle:ClientContact:index.html.twig', array(
            'contactsClient' => $entities,
        ));
         
    }  
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
            throw $this->createNotFoundException('L\'étude n\'existe pas !');

        if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        
        $clientcontact = new ClientContact;
        $clientcontact->setEtude($etude);
        $form        = $this->createForm(new ClientContactType, $clientcontact);
        $formHandler = new ClientContactHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
            return $this->redirect( $this->generateUrl('mgateSuivi_clientcontact_voir', array('id' => $clientcontact->getId())) );

        return $this->render('mgateSuiviBundle:ClientContact:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    private function compareDate(ClientContact $a,ClientContact $b) {
        if ($a->getDate() == $b->getDate())
            return 0;
        else
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $contactClient = $em->getRepository('mgateSuiviBundle:ClientContact')->find($id);

        if (!$contactClient)
            throw $this->createNotFoundException('Ce Contact Client n\'existe pas !');
		
		$etude = $contactClient->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $etude = $contactClient->getEtude();
        $contactsClient = $etude->getClientContacts()->toArray();
        usort($contactsClient, array($this, 'compareDate'));

        return $this->render('mgateSuiviBundle:ClientContact:voir.html.twig', array(
            'contactsClient'      => $contactsClient,
            'selectedContactClient' => $contactClient,
            'etude' => $etude,
            ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $clientcontact = $em->getRepository('mgate\SuiviBundle\Entity\ClientContact')->find($id) )
        {
            throw $this->createNotFoundException('Ce Contact Client n\'existe pas !');
        }
		
		$etude = $clientcontact->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $form        = $this->createForm(new ClientContactType, $clientcontact);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_clientcontact_voir', array('id' => $clientcontact->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:ClientContact:modifier.html.twig', array(
            'form' => $form->createView(),
            'clientcontact' => $clientcontact,
        ));
    }
}
