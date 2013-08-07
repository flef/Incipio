<?php
namespace mgate\FormationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\FormationBundle\Form\FormationType;

use mgate\FormationBundle\Entity\Formation;

class FormationController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('mgateFormationBundle:Formation')->findAll();
      
        return $this->render('mgateFormationBundle:Gestion:index.html.twig', array(
            'formations' => $entities,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('mgateFormationBundle:Formation')->findAll();
      
        return $this->render('mgateFormationBundle:Formations:lister.html.twig', array(
            'formations' => $entities,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('mgateFormationBundle:Formation')->find($id);
      
        return $this->render('mgateFormationBundle:Formations:voir.html.twig', array(
            'formation' => $entities,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
        {
            $formation = new Formation;
        }
      
        $form = $this->createForm(new FormationType, $formation);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($formation);
                $em->flush();
                
                $form = $this->createForm(new FormationType(), $formation);
            }
        }
        
        return $this->render('mgateFormationBundle:Gestion:modifier.html.twig', array(
            'form' => $form->createView(),
            'formation' => $formation,
        ));
    }
}