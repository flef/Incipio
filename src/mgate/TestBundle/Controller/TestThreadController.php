<?php

namespace mgate\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\TestBundle\Entity\TestThread;
use mgate\TestBundle\Form\TestThreadType;

/**
 * TestThread controller.
 *
 */
class TestThreadController extends Controller
{
    /**
     * Lists all TestThread entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateTestBundle:TestThread')->findAll();

        return $this->render('mgateTestBundle:TestThread:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a TestThread entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:TestThread')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestThread entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:TestThread:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new TestThread entity.
     *
     */
    public function newAction()
    {
        $entity = new TestThread();
        $form   = $this->createForm(new TestThreadType(), $entity);

        return $this->render('mgateTestBundle:TestThread:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new TestThread entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new TestThread();
        $form = $this->createForm(new TestThreadType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('testthread_show', array('id' => $entity->getId())));
        }

        return $this->render('mgateTestBundle:TestThread:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TestThread entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:TestThread')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestThread entity.');
        }

        $editForm = $this->createForm(new TestThreadType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateTestBundle:TestThread:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing TestThread entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateTestBundle:TestThread')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TestThread entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TestThreadType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('testthread_edit', array('id' => $id)));
        }

        return $this->render('mgateTestBundle:TestThread:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TestThread entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('mgateTestBundle:TestThread')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TestThread entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('testthread'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
