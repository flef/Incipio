<?php

namespace mgate\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\TestBundle\Entity\Relatedclass;
use mgate\TestBundle\Form\RelatedclassType;

/**
 * Relatedclass controller.
 *
 */
class RelatedclassController extends Controller
{
    /**
     * Lists all Relatedclass entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateTestBundle:Relatedclass')->findAll();

        return $this->render('mgateTestBundle:Relatedclass:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Relatedclass entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Relatedclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relatedclass entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:Relatedclass:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Relatedclass entity.
     *
     */
    public function newAction()
    {
        $entity = new Relatedclass();
        $form   = $this->createForm(new RelatedclassType(), $entity);

        return $this->render('mgateTestBundle:Relatedclass:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Relatedclass entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Relatedclass();
        $form = $this->createForm(new RelatedclassType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Relatedclass_show', array('id' => $entity->getId())));
        }

        return $this->render('mgateTestBundle:Relatedclass:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Relatedclass entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Relatedclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relatedclass entity.');
        }

        $editForm = $this->createForm(new RelatedclassType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:Relatedclass:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Relatedclass entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:Relatedclass')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relatedclass entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RelatedclassType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Relatedclass_edit', array('id' => $id)));
        }

        return $this->render('mgateTestBundle:Relatedclass:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Relatedclass entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('mgateTestBundle:Relatedclass')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Relatedclass entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('Relatedclass_'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
