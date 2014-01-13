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
        $entities = $em->getRepository('mgateFormationBundle:Formation')->findBy(array(), array('dateDebut' => 'DESC'));
      
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
        $formationsParMandat = $em->getRepository('mgateFormationBundle:Formation')->findAllByMandat();
              
        return $this->render('mgateFormationBundle:Formations:lister.html.twig', array(
            'formationsParMandat' => $formationsParMandat,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            throw $this->createNotFoundException('La formation n\'existe pas !');
      
        return $this->render('mgateFormationBundle:Formations:voir.html.twig', array(
            'formation' => $formation,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            $formation = new Formation;

      
        $form = $this->createForm(new FormationType, $formation);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
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
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if( ! $formation = $em->getRepository('mgate\FormationBundle\Entity\Formation')->find($id) )
            throw $this->createNotFoundException('La formation n\'existe pas !');

        $em->remove($formation);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgate_formations_lister', array()));
      
    }
}