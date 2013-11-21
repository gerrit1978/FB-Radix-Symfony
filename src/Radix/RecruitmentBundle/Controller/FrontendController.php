<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Radix\RecruitmentBundle\Entity\Job;

class FrontendController extends Controller
{
    public function frontendAction($accountid)
    {
    
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
        );
      }      
      return $this->render('RadixRecruitmentBundle:Frontend:frontend.html.twig', array('jobs' => $jobs_output));
    }
}
