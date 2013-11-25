<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Radix\RecruitmentBundle\Entity\Job;

class BackendController extends Controller
{
    // main page action
    public function backendAction($accountid)
    {
    
      // make a list of all jobs
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job');
      $jobs = $repository->findAll();
      
      $jobs_output = array();
      
      foreach ($jobs as $job) {
        $jobs_output[] = array(
          'title' => $job->getTitle(),
          'editlink' => $this->generateUrl('radix_backend_job_edit', array('accountid' => $accountid, 'id' => $job->getId())),
          'deletelink' => $this->generateUrl('radix_backend_job_delete', array('accountid' => $accountid, 'id' => $job->getId())),
        );
      }
      
    
      $links = array(
        'addJob' => $this->generateUrl('radix_backend_job_add', array('accountid' => $accountid)),
      );
        
      return $this->render('RadixRecruitmentBundle:Backend:backend.html.twig', array('account' => $accountid, 'links' => $links, 'jobs' => $jobs_output));
    }
    
    // job add action
    public function jobAddAction(Request $request, $accountid) {
    
      $time = time();
     
      $job = new Job();
      $job->setAccountid($accountid);
      $job->setGuid($accountid . $time);
      $job->setSource('admin');
      $job->setCreated($time);
      
      $form = $this->createFormBuilder($job)
        ->add('title', 'text')
        ->add('description', 'textarea')
        ->add('Save', 'submit')
        ->getForm();
    
      $form->handleRequest($request);
    
      if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
        
        return $this->redirect($this->generateUrl('radix_backend_job_add_success', array('accountid' => $accountid)));
      }
    
      return $this->render('RadixRecruitmentBundle:Backend:jobAdd.html.twig', array('form' => $form->createView()));
    }
    
    // job add success action
    public function jobAddSuccessAction($accountid) {
      return $this->render('RadixRecruitmentBundle:Backend:jobAddSuccess.html.twig', array('account' => $accountid));
    }
    
}
