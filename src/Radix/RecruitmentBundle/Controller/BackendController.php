<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Config;
use Radix\RecruitmentBundle\Entity\Media;

class BackendController extends Controller
{
/**********************************************************************************************************/

    /**
     * Controller action for frontpage
     * - redirect to jobs index page
     **/
    public function indexAction(Request $request, $accountid)
    {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      /**** SERVICES END ****/      
      
      return $this->redirect($this->generateUrl('radix_backend_jobs', array('accountid' => $accountid)));
    }

/**********************************************************************************************************/

    /**
     * Controller action for jobspage
     * - show all jobs
     **/
    public function jobsAction(Request $request, $accountid)
    {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
      // make a list of all jobs
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job');
      $jobs = $repository->findAll();
      
      $jobs_output = array();
      
      $delta = 0;
      $class = "odd";
      foreach ($jobs as $job) {
        if (($delta % 2) == 0) {
          $class = "even";
        } else {
          $class = "odd";
        }
      
        $jobs_output[] = array(
          'title' => "<a href='" . $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $job->getId())) . "'>" . $job->getTitle() . "</a>",
          'editLink' => $this->generateUrl('radix_backend_job_edit', array('accountid' => $accountid, 'id' => $job->getId())),
          'deleteLink' => $this->generateUrl('radix_backend_job_delete', array('accountid' => $accountid, 'id' => $job->getId())),
          'applicationsLink' => $this->generateUrl('radix_backend_job_applications', array('accountid' => $accountid, 'id' => $job->getId())),
          'class' => $class,
        );
        $delta++;
      }
      
      $carrot['jobs'] = $jobs_output;
      $carrot['pageLinks']['addJob'] = "<a href='" . $this->generateUrl('radix_backend_job_add', array('accountid' => $accountid)) . "' class='add-job'>Add a job</a>";

      return $this->render('RadixRecruitmentBundle:Backend:jobs.html.twig', array('carrot' => $carrot));
    }

/**********************************************************************************************************/

    /**
     * Controller action for overview applications page
     * - show all applications
     **/
    public function applicationsAction(Request $request, $accountid)
    {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
      // make a list of all applications
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application');
      $applications = $repository->findAll();
      
      $applications_output = array();
      
      $delta = 0;
      $class = "odd";
      foreach ($applications as $application) {

        if (($delta % 2) == 0) {
          $class = "even";
        } else {
          $class = "odd";
        }
      
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
          'jobLink' => "<a href='" . $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $job->getId())) . "'>" . $job->getTitle() . "</a>",
          'detailLink' => $application_detail_link,
          'class' => $class,
        );
        
        $delta++;
      }
      
      $carrot['applications'] = $applications_output;
    
      return $this->render('RadixRecruitmentBundle:Backend:applications.html.twig', array('carrot' => $carrot));
    }

/**********************************************************************************************************/

    /**
     * Controller action for configuration
     **/
    public function configAction(Request $request, $accountid)
    {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

      // render the config object into a form
      $config = $carrot['config'];
      
      $form = $this->createFormBuilder($config)
        ->add('accountid')
        ->add('xmlurl')
        ->add('xmluser')
        ->add('xmlpass')
        ->add('xmlroot')
        ->add('pageid')
        ->add('pagetitle')
        ->add('Save', 'submit')
        ->getForm();
      
      $form->handleRequest($request);
      
      if ($form->isValid()) {
        
        // save the config object to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($config);
        $em->flush();
        
        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved.');
        
        return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
        
      }
          
      return $this->render('RadixRecruitmentBundle:Backend:config.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));
    }
    
 /**********************************************************************************************************/

    /**
     * Controller action for applications per job
     **/
    public function jobApplicationsAction(Request $request, $accountid, $id)
    {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

      // get the job
      $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($id);
      
      if (!$job) {
        throw $this->createNotFoundException('No job found.');
      }
      
      $carrot['job'] = array(
        'title' => $job->getTitle(),
      );

      // get the applications for this job
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application');
      $query = $repository->createQueryBuilder('a')
        ->where('a.accountid = :accountid')
        ->andWhere('a.jobid = :jobid')
        ->setParameters(array(
          'accountid' => $accountid,
          'jobid' => $id
        ))
        ->getQuery();
      
      $applications = $query->getResult();

      $applications_output = array();

      if (is_array($applications) && count($applications)) {
	      $delta = 0;
	      $class = "odd";
        foreach ($applications as $application) {
	        if (($delta % 2) == 0) {
	          $class = "even";
	        } else {
	          $class = "odd";
	        }

          $applications_output[] = array(
            'name' => $application->getName(),
            'email' => $application->getEmail(),
            'city' => $application->getCity(),
            'detailLink' => "<a href='" . $this->generateUrl('radix_backend_application_detail', array('accountid' => $accountid, 'applicationid' => $application->getId())) . "'>Bekijk</a>",
            'class' => $class,
          );
          $delta++;
        }
      }
      
      $carrot['applications'] = $applications_output;
          
      return $this->render('RadixRecruitmentBundle:Backend:jobApplications.html.twig', array('carrot' => $carrot));
    }
   
    
    
    
    
 /**********************************************************************************************************/

    /**
     * Controller action for application detail
     **/
    public function applicationDetailAction(Request $request, $accountid, $applicationid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

      // get application detail
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application');
      $query = $repository->createQueryBuilder('a')
        ->where('a.accountid = :accountid')
        ->andWhere('a.id = :applicationid')
        ->setParameters(array(
          'accountid' => $accountid,
          'applicationid' => $applicationid
        ))
        ->getQuery();
      
      $applications = $query->getResult();

      if (!$applications || (is_array($applications) && (!count($applications)))) {
        throw $this->createNotFoundException(
          'No application found for this id ' . $applicationid . '.'
        );
      }

      $application = $applications[0];


      // parse the application data
      $name = $application->getName();
      $email = $application->getEmail();
      $city = $application->getCity();
      
      $application_output = array(
        'name' => $name,
        'email' => $email,
        'city' => $city,
      );

      $carrot['application'] = $application_output;
      
      // parse the job data
      $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($application->getJobid());
      
      $carrot['job'] = array(
        'title' => $job->getTitle(),
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
      
      $carrot['work'] = $work_output;
      
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
      
      $carrot['education'] = $education_output;
      
      return $this->render('RadixRecruitmentBundle:Backend:applicationDetail.html.twig', array('carrot' => $carrot));


    }
    
 /**********************************************************************************************************/

    /**
     * Controller action for adding a job
     **/
    public function jobAddAction(Request $request, $accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
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

        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'The job was added.');
        
        // post to fb wall
        // $helper = $this->get('radix.helper.facebook');
        // $params = array('title' => $job->getTitle());
        // $helper->post($params);

        return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
      }
    
      return $this->render('RadixRecruitmentBundle:Backend:jobAdd.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));
    }
    
 /**********************************************************************************************************/

    /**
     * Controller action for deleting a job
     **/
    public function jobDeleteAction(Request $request, $accountid, $id) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
      $em = $this->getDoctrine()->getManager();
      $job = $em->getRepository('RadixRecruitmentBundle:Job')->find($id);
      
      if (!$job) {
        throw $this->createNotFoundException(
          'No job found for this id ' . $id . '.'
        );
      }
      
      // delete object from db
      $em->remove($job);
      $em->flush();

      // add flash message
      $this->get('session')->getFlashBag()->add('notice', 'The job was deleted.');
      
      // Delete all application entries related to this job
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application');
      $query = $repository->createQueryBuilder('a')
        ->delete()
        ->where('a.jobid = :jobid')
        ->setParameters(array(
          'jobid' => $id
        ))
        ->getQuery();
      $result = $query->getResult();
      // Delete all work entries related to this job
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Work');
      $query = $repository->createQueryBuilder('w')
        ->delete()
        ->where('w.jobid = :jobid')
        ->setParameters(array(
          'jobid' => $id
        ))
        ->getQuery();
      $result = $query->getResult();
      // Delete all education entries related to this job
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Education');
      $query = $repository->createQueryBuilder('e')
        ->delete()
        ->where('e.jobid = :jobid')
        ->setParameters(array(
          'jobid' => $id
        ))
        ->getQuery();
      $result = $query->getResult();
      
      
      return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
    }
    
 /**********************************************************************************************************/

    /**
     * Controller action for editing a job
     **/
    public function jobEditAction(Request $request, $accountid, $id) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
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
      
        // persist object to db
        $em->persist($job);
        $em->flush();

        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'The job was saved.');
        
        return $this->redirect($this->generateurl('radix_backend', array('accountid' => $accountid)));
      }
      
      $carrot['job'] = array(
        'title' => $job->getTitle(),
      );
      
      return $this->render('RadixRecruitmentBundle:Backend:jobEdit.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));
      
    }

 /**********************************************************************************************************/

    /**
     * Controller action for adding a media file
     **/
    public function mediaAddAction(Request $request, $accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
      $time = time();
     
      $media = new Media();
      $media->setCreated($time);
      
      $form = $this->createFormBuilder($media)
        ->add('filename', 'text')
        ->add('filepath', 'file')
        ->add('Save', 'submit')
        ->getForm();
    
/*
      $form->handleRequest($request);
    
      if ($form->isValid()) {
      
        // persist object to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();

        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'The job was added.');
        
        // post to fb wall
        // $helper = $this->get('radix.helper.facebook');
        // $params = array('title' => $job->getTitle());
        // $helper->post($params);

        return $this->redirect($this->generateUrl('radix_backend', array('accountid' => $accountid)));
      }
*/
    
      return $this->render('RadixRecruitmentBundle:Backend:mediaAdd.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));
    }


    
}

