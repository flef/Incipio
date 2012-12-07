<?php
// src/Sdz/BlogBundle/Form/ArticleHandler.php

namespace mgate\SuiviBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;

class ApHandler
{
    protected $form;
    protected $request;
    protected $em;

    public function __construct(Form $form, Request $request, EntityManager $em)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
    }

    public function process()
    {
        if( $this->request->getMethod() == 'POST' )
        {
            $this->form->bindRequest($this->request);

            if( $this->form->isValid() )
            {
                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    public function onSuccess(Ap $ap)
    {
        
        $this->em->persist($ap);
        $this->em->flush();
    }
}

