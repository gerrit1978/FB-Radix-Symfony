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
      
      // make a list of all applications
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application');
      $applications = $repository->findAll();
      
      $applications_output = array();
      
      foreach ($applications as $application) {
        $applications_output[] = array(
          'name' => $application->getName(),
          'email' => $application->getEmail(),
          'city' => $application->getCity(),
        );
      }
    
      $links = array(
        'addJob' => $this->generateUrl('radix_backend_job_add', array('accountid' => $accountid)),
      );
        
      return $this->render('RadixRecruitmentBundle:Backend:backend.html.twig', array('account' => $accountid, 'links' => $links, 'jobs' => $jobs_output, 'applications' => $applications_output));
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
      
        // persist object to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
        
        // post to fb wall
        $helper = $this->get('radix.helper.facebook');
        $params = array('title' => $job->getTitle());
        $helper->post($params);

        return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
      }
    
      return $this->render('RadixRecruitmentBundle:Backend:jobAdd.html.twig', array('form' => $form->createView()));
    }
    
    // job delete action
    public function jobDeleteAction(Request $request, $accountid, $id) {
      $em = $this->getDoctrine()->getManager();
      $job = $em->getRepository('RadixRecruitmentBundle:Job')->find($id);
      
      if (!$job) {
        throw $this->createNotFoundException(
          'No job found for this id ' . $id . '.'
        );
      }
      
      $em->remove($job);
      $em->flush();
      
      return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
    }
    
    // job edit action
    public function jobEditAction(Request $request, $accountid, $id) {
      $em = $this->getDoctrine()->getManager();
      $job = $em->getRepository('RadixRecruitmentBundle:Job')->find($id);
      
      if (!$job) {
        throw $this->createNotFoundException(
          'No job found for this id ' . $id . '.'
        );
      }
      
      $form = $this->createFormBuilder($job)
        ->add('title', 'text')
        ->add('description', 'textarea')
        ->add('Save', 'submit')
        ->getForm();
      
      $form->handleRequest($request);
      
      if ($form->isValid()) {
        $em->persist($job);
        $em->flush();
        
        return $this->redirect($this->generateurl('radix_backend', array('accountid' => $accountid)));
      }
      
      return $this->render('RadixRecruitmentBundle:Backend:jobEdit.html.twig', array('form' => $form->createView()));
      
    }
    
    
}
