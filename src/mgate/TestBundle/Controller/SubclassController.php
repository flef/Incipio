<?php

namespace mgate\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\TestBundle\Entity\Subclass;
use mgate\TestBundle\Form\SubclassType;

/**
 * Subclass controller.
 *
 */
class SubclassController extends Controller
{
    /**
     * Lists all Subclass entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateTestBundle:Subclass')->findAll();

        return $this->render('mgateTestBundle:Subclass:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Subclass entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Subclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subclass entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:Subclass:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Subclass entity.
     *
     */
    public function newAction()
    {
        $entity = new Subclass();
        $form   = $this->createForm(new SubclassType(), $entity);

        return $this->render('mgateTestBundle:Subclass:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Subclass entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Subclass();
        $form = $this->createForm(new SubclassType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subclass_show', array('id' => $entity->getId())));
        }

        return $this->render('mgateTestBundle:Subclass:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Subclass entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Subclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subclass entity.');
        }

        $editForm = $this->createForm(new SubclassType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:Subclass:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Subclass entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Subclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subclass entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SubclassType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subclass_edit', array('id' => $id)));
        }

        return $this->render('mgateTestBundle:Subclass:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Subclass entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('mgateTestBundle:Subclass')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Subclass entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('subclass'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
