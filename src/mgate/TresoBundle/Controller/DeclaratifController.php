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


namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\TresoBundle\Entity\Facture;
use Symfony\Component\HttpFoundation\Request;


class DeclaratifController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bvs = $em->getRepository('mgateTresoBundle:BV')->findAll();
        
        return $this->render('mgateTresoBundle:BV:index.html.twig', array('bvs' => $bvs));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function TVAAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
    
        $data = array();
        $tvaCollectee   = array();
        $tvaDeductible = array();
        $totalTvaCollectee = array('HT' => 0, 'TTC' => 0, 'TVA' => 0);
        $totalTvaDeductible = array('HT' => 0, 'TTC' => 0, 'TVA' => 0);
        $tvas = array();
        
        $defaultData = array('message' => 'Date');
        $form = $this->createFormBuilder($defaultData)
                      ->add(
                          'date', 
                          'genemu_jquerydate',
                          array(
                              'label'=>'Mois considéré',
                              'required'=>true, 'widget'=>'single_text',
                              'data'=>date_create(),'format' => 'dd/MM/yyyy',))
                       ->add('trimestriel', 'checkbox', array('label' => 'Trimestriel ?', 'required' => false))
                       ->getForm();

        $periode = '';
        $nfs = array();
        $fas = array();
        $fvs = array();
        
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            $data = $form->getData();
            $date = $data["date"];
            $month = $date->format('m');
            $year = $date->format('Y');
        }else{
            $date = new \DateTime('now');
            $month = $date->format('m');
            $year = $date->format('Y');            
        }
        setlocale(LC_TIME, 'fra_fra');
        if(array_key_exists('trimestriel', $data) && $data['trimestriel']){            
            $periode = 'Déclaratif pour la période : '.utf8_encode(strftime('%B', $date->format('U')).' - '.strftime('%B', $date->modify('+2 month')->format('U')));
            for($i = 0; $i < 3; $i++){
                $nfs = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findAllByMonth($month, $year, true);
                $fas = $em->getRepository('mgateTresoBundle:Facture')->findAllTVAByMonth(Facture::$TYPE_ACHAT, $month, $year, true);
                $fvs = $em->getRepository('mgateTresoBundle:Facture')->findAllTVAByMonth(Facture::$TYPE_VENTE, $month, $year, true);
            }            
        }            
        else{
            $periode = 'Déclaratif pour la période : '.utf8_encode(strftime('%B', $date->format('U')));
            $nfs = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findAllByMonth($month, $year);
            $fas = $em->getRepository('mgateTresoBundle:Facture')->findAllTVAByMonth(Facture::$TYPE_ACHAT, $month, $year);
            $fvs = $em->getRepository('mgateTresoBundle:Facture')->findAllTVAByMonth(Facture::$TYPE_VENTE, $month, $year);
        }
            

        
        
        
        /**
         * TVA DEDUCTIBLE
         */
        foreach (array($fas, $nfs) as $entityDeductibles ){
            foreach ($entityDeductibles as $entityDeductible){
                $montantTvaParType = array();
                $montantHT = 0;
                $montantTTC = 0;
                foreach ($entityDeductible->getDetails() as $entityDeductibled){
                    $tauxTVA = $entityDeductibled->getTauxTVA();                    
                    if(key_exists($tauxTVA, $montantTvaParType))
                        $montantTvaParType[$tauxTVA] += $entityDeductibled->getMontantTVA();
                    else
                        $montantTvaParType[$tauxTVA] = $entityDeductibled->getMontantTVA();
                    $montantHT  += $entityDeductibled->getMontantHT();
                    $montantTTC += $entityDeductibled->getMontantTTC(); 
                    
                    // mise à jour des montant Globaux
                    $totalTvaDeductible['HT']  += $entityDeductibled->getMontantHT();
                    $totalTvaDeductible['TTC'] += $entityDeductibled->getMontantTTC();
                    $totalTvaDeductible['TVA'] += $entityDeductibled->getMontantTVA();
                    
                    // Mise à jour du montant global pour le taux de TVA ciblé
                    if(!in_array($tauxTVA, $tvas) && $tauxTVA != null)$tvas[] = $tauxTVA;
                    if(!key_exists($tauxTVA, $totalTvaDeductible))
                        $totalTvaDeductible[$tauxTVA] = $entityDeductibled->getMontantTVA();
                    else
                        $totalTvaDeductible[$tauxTVA] += $entityDeductibled->getMontantTVA();
                }
                $tvaDeductible[] = array('DATE' => $entityDeductible->getDate(), 'LI'=> $entityDeductible->getReference(),'HT' => $montantHT, 'TTC' => $montantTTC, 'TVA' => $entityDeductible->getMontantTVA(), 'TVAT' => $montantTvaParType);
                
            }
        }
       
        /**
         * TVA COLLECTE
         */
        foreach ($fvs as $fv){
            $montantTvaParType = array();
            
            $montantHT = $fv->getMontantHT();
            $montantTTC = $fv->getMontantTVA();
            
            // Mise à jour du montant global pour le taux de TVA ciblé 
            $totalTvaCollectee['HT']  += $fv->getMontantHT();
            $totalTvaCollectee['TTC'] += $fv->getMontantTTC();
            $totalTvaCollectee['TVA'] += $fv->getMontantTVA();
            
            foreach ($fv->getDetails() as $fvd){
                $tauxTVA = $fvd->getTauxTVA();
                if(key_exists($tauxTVA, $montantTvaParType))
                    $montantTvaParType[$tauxTVA] += $fvd->getMontantTVA();
                else
                    $montantTvaParType[$tauxTVA] = $fvd->getMontantTVA();

                if(!key_exists($tauxTVA, $totalTvaCollectee))
                    $totalTvaCollectee[$tauxTVA] = $fvd->getMontantTVA();
                else
                    $totalTvaCollectee[$tauxTVA] += $fvd->getMontantTVA();
                
                // Ajout de l'éventuel nouveau taux de TVA à la liste des taux
                if(!in_array($tauxTVA, $tvas) && $tauxTVA != null)$tvas[] = $tauxTVA;
            }
            if($md = $fv->getMontantADeduire()){
                $tauxTVA = $md->getTauxTVA();
                if(key_exists($tauxTVA, $montantTvaParType))
                    $montantTvaParType[$tauxTVA] -= $md->getMontantTVA();
                else
                    $montantTvaParType[$tauxTVA] = - $md->getMontantTVA();

                if(!key_exists($tauxTVA, $totalTvaCollectee))
                    $totalTvaCollectee[$tauxTVA] = - $md->getMontantTVA();
                else
                    $totalTvaCollectee[$tauxTVA] -= $md->getMontantTVA();
                
                // Ajout de l'éventuel nouveau taux de TVA à la liste des taux
                if(!in_array($tauxTVA, $tvas) && $tauxTVA != null)$tvas[] = $tauxTVA;
            }

            $tvaCollectee[] = array('DATE' => $fv->getDate(),'LI'=> $fv->getReference(),'HT' => $montantHT, 'TTC' => $montantTTC,'TVA' => $fv->getMontantTVA() , 'TVAT' => $montantTvaParType);
            
           
        }
        sort($tvas);      
        return $this->render('mgateTresoBundle:Declaratif:TVA.html.twig', 
            array('form' => $form->createView(),
                'tvas' => $tvas, 
                'tvaDeductible' => $tvaDeductible, 
                'tvaCollectee' => $tvaCollectee,
                'totalTvaDeductible' => $totalTvaDeductible,
                'totalTvaCollectee' => $totalTvaCollectee,
                'periode' => $periode,
                )
            );
    }
    
/**
     * @Secure(roles="ROLE_CA")
     */
    public function BRCAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $defaultData = array('message' => 'Date');
        $form = $this->createFormBuilder($defaultData)
                      ->add(
                          'date', 
                          'genemu_jquerydate',
                          array(
                              'label'=>'Mois du déclaratif',
                              'required'=>true, 'widget'=>'single_text',
                              'data'=>date_create(),'format' => 'dd/MM/yyyy',)
                      )->getForm();

        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            $data = $form->getData();
            $date = $data["date"];
            $month = $date->format('m');
            $year = $date->format('Y');
        }else{
            $date = new \DateTime('now');
            $month = $date->format('m');
            $year = $date->format('Y');
        }
        
        $bvs = $em->getRepository('mgateTresoBundle:BV')->findAllByMonth($month, $year);
        
        $salarieRemunere = array();
        foreach ($bvs as $bv){
            $id = $bv->getMission()->getIntervenant()->getIdentifiant();
            $salarieRemunere[$id] = 1;
        }
        $nbSalarieRemunere = count($salarieRemunere);
        
        return $this->render('mgateTresoBundle:Declaratif:BRC.html.twig', 
            array('form' => $form->createView(), 'bvs' => $bvs, 'nbSalarieRemunere' => $nbSalarieRemunere)
            );
    }
}