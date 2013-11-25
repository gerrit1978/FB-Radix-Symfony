<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Radix\RecruitmentBundle\Entity\Job;

class FrontendController extends Controller
{

    /** CONTROLLER ACTION FOR "FRONTPAGE": overview page of jobs **/
    public function frontendAction($accountid)
    {
      $helper = $this->get('radix.helper.facebook');
      $isPageAdmin = $helper->isPageAdmin();

      /* We get the config parameters (XML URL/USER/PASS) */
      $config = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
 
      if (!$config) {
        throw $this->createNotFoundException('No config found for this accountid.');
      }

      /* We get the jobs */
      $jobs = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findBy(array('accountid' => $accountid));

      $jobs_output = array();
      foreach ($jobs as $job) {
        $jobs_output[] = array(
          'title' => $job->getTitle(),
          'description' => $job->getDescription(),
          'pagelink' => $this->generateUrl('radix_detail', array('accountid' => $accountid, 'id' => $job->getId())),
          'applink' => 'http://apps.facebook.com/radix-symfony/job/' . $job->getGuid(),
          'onlineSince' => date('d.m.Y', $job->getCreated()),
        );
      }
      
      if ($isPageAdmin) {
        $adminLink = $this->generateUrl('radix_backend', array('accountid' => $accountid));
      } else {
        $adminLink = "";
      }
      
      
      return $this->render('RadixRecruitmentBundle:Frontend:frontend.html.twig', array('jobs' => $jobs_output, 'adminLink' => $adminLink));
    }
    
    /** CONTROLLER ACTION FOR JOB DETAIL PAGE **/
    public function detailAction($accountid, $id) {
    
      $config = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
      
      if (!$config) {
        throw $this->createNotFoundException('No config found for this accountid.');
      }
      
      // get the job details
      $job = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findOneBy(array('id' => $id));
      
      $job_output = array('title' => $job->getTitle(), 'description' => $job->getDescription());
      
      $overview_link = $this->generateUrl('radix_frontend', array('accountid' => $accountid));
    
      return $this->render('RadixRecruitmentBundle:Frontend:detail.html.twig', array('job' => $job_output, 'overview_link' => $overview_link));
    }
}
