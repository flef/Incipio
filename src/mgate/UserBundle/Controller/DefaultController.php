<?php

namespace mgate\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\UserBundle\Form\UserAdminType;

class DefaultController extends Controller
{
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction($name)
    {
        return $this->render('mgateUserBundle:Default:index.html.twig', array('name' => $name));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        

        $entities = $em->getRepository('mgateUserBundle:User')->findAll();
                
        return $this->render('mgateUserBundle:Default:lister.html.twig', array('users' => $entities));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('mgateUserBundle:User')->find($id); 
        if (!$user) {
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas !');
        }
        
        return $this->render('mgateUserBundle:Default:voir.html.twig', array('user' => $user));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('mgateUserBundle:User')->find($id);
        if (!$user)
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas !');
        
        if($user->getId() == 1)
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Impossible de modifier le Super Administrateur. Contactez support@incipio.fr pour toute modification.');


            $form = $this->createForm(new UserAdminType('mgate\UserBundle\Entity\User'), $user);
        $deleteForm = $this->createDeleteForm($id);
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                
                $em->persist($user);
                $em->flush();
                
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->refreshUser($user);
                
                
                return $this->redirect( $this->generateUrl('mgate_user_voir', array('id' => $user->getId())) );
            }
                
        }
        
        return $this->render('mgateUserBundle:Default:modifier.html.twig', array(
            'form' => $form->createView(), 
            'delete_form' => $deleteForm->createView(),
            ));       
        
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
   
            if( ! $entity = $em->getRepository('mgate\UserBundle\Entity\User')->find($id) )
                throw $this->createNotFoundException('L\'utilisateur n\'existe pas !');
            
            if($entity->getId() == 1)
                throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Impossible de supprimer le Super Administrateur. Contactez support@incipio.fr pour toute modification.');

            
            if($entity->getPersonne())
                $entity->getPersonne()->setUser(null);
            $entity->setPersonne(null);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgate_user_lister'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
