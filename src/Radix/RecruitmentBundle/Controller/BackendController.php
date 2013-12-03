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
      
        // get application id
        $applicationid = $application->getId();
      
        // get job title for this application
        $application_jobid = $application->getJobid();
        $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($application_jobid);
      
        // define detail link
        $application_detail_link = $this->generateUrl('radix_backend_application_detail', array('accountid' => $accountid, 'applicationid' => $applicationid));
      
        $applications_output[] = array(
          'name' => $application->getName(),
          'email' => $application->getEmail(),
          'city' => $application->getCity(),
          'jobtitle' => $job->getTitle(),
          'detaillink' => $application_detail_link,
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
        ->add('industry', 'text')
        ->add('location', 'text')
        ->add('Save', 'submit')
        ->getForm();
    
      $form->handleRequest($request);
    
      if ($form->isValid()) {
      
        // persist object to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
        
/*
        // post to fb wall
        $helper = $this->get('radix.helper.facebook');
        $params = array('title' => $job->getTitle());
        $helper->post($params);
*/

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
        ->add('industry', 'text')
        ->add('location', 'text')
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
    
    
    // application detail action
    public function applicationDetailAction(Request $request, $accountid, $applicationid) {

      $application = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application')->find($applicationid);

      if (!$application) {
        throw $this->createNotFoundException(
          'No application found for this id ' . $applicationid . '.'
        );
      }
      
      // parse the application data
      $name = $application->getName();
      $email = $application->getEmail();
      $city = $application->getCity();
      
      $application_output = array(
        'name' => $name,
        'email' => $email,
        'city' => $city,
      );
      
      // parse the job data
      $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($application->getJobid());
      $job_title = $job->getTitle();
      
      $job_output = array(
        'title' => $job_title,
      );
      
      // parse the work data
      $work_items = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Work')->findBy(array('applicationid' => $applicationid));
      
      $work_output = array();
      if (is_array($work_items) && count($work_items)) {
        foreach ($work_items as $work) {
          $work_output[] = array(
            'employer' => $work->getEmployer(),
            'location' => $work->getLocation(),
            'position' => $work->getPosition(),
          );
        }
      }
      
      // parse the education data
      $education_items = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Education')->findBy(array('applicationid' => $applicationid));
      
      $education_output = array();
      if (is_array($education_items) && count($education_items)) {
        foreach ($education_items as $education) {
          $education_output[] = array(
            'school' => $education->getSchool(),
            'year' => $education->getYear(),
            'type' => $education->getType(),
          );
        }
      }
      
      return $this->render('RadixRecruitmentBundle:Backend:applicationDetail.html.twig', array(
        'application_output' => $application_output, 
        'work_output' => $work_output,
        'education_output' => $education_output,
        'job_output' => $job_output,
      ));


    }
    
    
}
