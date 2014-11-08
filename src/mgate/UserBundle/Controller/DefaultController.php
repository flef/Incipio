<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


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


    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addUserFromPersonneAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $personne = $em->getRepository('mgatePersonneBundle:Personne')->find($id);

        if(!$personne)
            throw $this->createNotFoundException('La personne n\'existe pas !');

        if($personne->getUser())
            throw new \Exception("Un utilisateur est déjà liée à cette personne !");
        if(!$personne->getEmail())
            throw new \Exception("l'utilisateur n'a pas d'email valide !");

        $temporaryPassword = md5(mt_rand());
        $token = sha1(uniqid(mt_rand(), true));
        
        /* Génération de l'user */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setPersonne($personne);
        $user->setEmail($personne->getEmail());
        $user->setPlainPassword($temporaryPassword);
        // Utilisateur à confirmer
        $user->setEnabled(false);
        $user->setConfirmationToken($token);
        // \\
        $user->setUsername($this->enMinusculeSansAccent($personne->getPrenom().'.'.$personne->getNom()));
        /**/

        $userManager->updateUser($user); // Pas besoin de faire un flush (ça le fait tout seul)

        /* Envoie d'un email de confirmation */
        $mailer = $this->container->get('fos_user.mailer');
        $mailer->sendConfirmationEmailMessage($user);   
        $mailer->sendResettingEmailMessage($user);         

        return $this->redirect( $this->generateUrl('mgate_user_lister'));
    }

    private function enMinusculeSansAccent($texte){
        $texte = mb_strtolower($texte, 'UTF-8');
        $texte = str_replace(
            array(
                'à', 'â', 'ä', 'á', 'ã', 'å',
                'î', 'ï', 'ì', 'í', 
                'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
                'ù', 'û', 'ü', 'ú', 
                'é', 'è', 'ê', 'ë', 
                'ç', 'ÿ', 'ñ', 
            ),
            array(
                'a', 'a', 'a', 'a', 'a', 'a', 
                'i', 'i', 'i', 'i', 
                'o', 'o', 'o', 'o', 'o', 'o', 
                'u', 'u', 'u', 'u', 
                'e', 'e', 'e', 'e', 
                'c', 'y', 'n', 
            ),
            $texte
        );
        return $texte;        
    }
}
