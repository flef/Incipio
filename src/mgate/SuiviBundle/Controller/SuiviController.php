<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;

use mgate\SuiviBundle\Entity\Suivi;
use mgate\SuiviBundle\Form\SuiviType;


class ClientContactController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Suivi')->findBy(array(), array('date' => 'DESC'));

        return $this->render('mgateSuiviBundle:Suivi:index.html.twig', array(
            'suivis' => $entities,
        ));
         
    }  
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');

        
        
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
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $contactClient = $em->getRepository('mgateSuiviBundle:ClientContact')->find($id);

        if (!$contactClient) {
            throw $this->createNotFoundException('Unable to find ClientContact entity.');
        }

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
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $clientcontact = $em->getRepository('mgate\SuiviBundle\Entity\ClientContact')->find($id) )
        {
            throw $this->createNotFoundException('ClientContact[id='.$id.'] inexistant');
        }

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
