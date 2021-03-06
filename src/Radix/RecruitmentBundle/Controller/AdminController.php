<?php

/*
 * Copyright 2014 - Gerrit Vos & Arne Vanvlasselaer
 * Radix Recruitment
 */

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Radix\RecruitmentBundle\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction() {

      return $this->render('RadixRecruitmentBundle:Admin:index.html.twig');
    }
    
    public function applicationsAction() {
    
      // get config
      $configs = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Config')->findAll();
      
      $config_output = array();
      
      foreach ($configs as $config) {
        $id = $config->getId();
        $accountid = $config->getAccountid();
        $pagetitle = $config->getPagetitle();
        
        $config_output[] = array(
          'link' => $this->generateUrl('radix_admin_application_edit', array('id' => $id)),
          'accountid' => $accountid,
          'pagetitle' => $pagetitle,
        );
      }
      
      return $this->render('RadixRecruitmentBundle:Admin:applications.html.twig', array('configs' => $config_output));
    }
    
    public function applicationEditAction(Request $request, $id) {
      $config = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Config')->find($id);
      
      $form = $this->createFormBuilder($config)
        ->add('accountid')
        ->add('xmlurl')
        ->add('xmluser')
        ->add('xmlpass')
        ->add('xmlroot')
        ->add('pageid')
        ->add('pagetitle')
        ->add('pageurl')
        ->add('employerid')
        ->add('applymail')
        ->add('linkedinid')
        ->add('accesstoken')
        ->add('Save', 'submit')
        ->getForm();
      
      $form->handleRequest($request);

      if ($form->isValid()) {
        
        // save the config object to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($config);
        $em->flush();
        
        return $this->redirect($this->generateUrl('radix_admin_index'));
        
      }


      return $this->render('RadixRecruitmentBundle:Admin:applicationEdit.html.twig', array('form' => $form->createView()));
      
    }
    
    public function applicationNewAction(Request $request) {
      $config = new Config();

      $form = $this->createFormBuilder($config)
        ->add('accountid')
        ->add('xmlurl')
        ->add('xmluser')
        ->add('xmlpass')
        ->add('xmlroot')
        ->add('pageid')
        ->add('pagetitle')
        ->add('pageurl')
        ->add('employerid')
        ->add('applymail')
        ->add('linkedinid')
        ->add('accesstoken')
        ->add('Save', 'submit')
        ->getForm();
   
      $form->handleRequest($request);

      if ($form->isValid()) {
        
        // save the config object to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($config);
        $em->flush();
        
        return $this->redirect($this->generateUrl('radix_admin_index'));
        
      }


      return $this->render('RadixRecruitmentBundle:Admin:applicationNew.html.twig', array('form' => $form->createView()));
    }

    public function watchdogAction(Request $request) {

      // get watchdog entries
      $watchdogs = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Watchdog')->findAll();
      
      $watchdog_output = array();
      
      foreach ($watchdogs as $watchdog) {
        $id = $watchdog->getId();
        $accountid = $watchdog->getAccountid();
        $created = date('d.m.Y H:i', $watchdog->getCreated());
        $type = $watchdog->getType();
        $message = $watchdog->getMessage();
        
        $watchdog_output[] = array(
          'id' => $id,
          'accountid' => $accountid,
          'created' => $created,
          'type' => $type,
          'message' => $message,
        );
      }
      
      return $this->render('RadixRecruitmentBundle:Admin:watchdogs.html.twig', array('watchdogs' => $watchdog_output));
    }

    
}
