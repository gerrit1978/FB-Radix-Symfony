<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Config;
use Radix\RecruitmentBundle\Entity\Document;

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
      $jobs = $repository->createQueryBuilder('j')
        ->where('j.accountid = :accountid')
        ->setParameter('accountid', $accountid)
        ->orderBy('j.created', 'DESC')
        ->getQuery()
        ->getResult();
      
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
      $carrot['pageLinks']['addJob'] = "<a href='" . $this->generateUrl('radix_backend_job_add', array('accountid' => $accountid)) . "' class='add-job'>Job toevoegen</a>";

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
      $applications = $repository->createQueryBuilder('a')
        ->where('a.accountid = :accountid')
        ->setParameter('accountid', $accountid)
        ->orderBy('a.created', 'DESC')
        ->getQuery()
        ->getResult();
      
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
        
        if ($application_jobid == '-1') {
          $job_link = "Spontaan solliciteren";
        } else {
          $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($application_jobid);        
          if (!$job) {
            $job_title = "-- job werd verwijderd --";
            $job_link = $job_title;
          } else {
	          $job_title = $job->getTitle();
	          $job_link = "<a href='" . $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $application_jobid)) . "'>" . $job_title . "</a>";
	        }
        }
      
        // define detail link
        $application_detail_link = $this->generateUrl('radix_backend_application_detail', array('accountid' => $accountid, 'applicationid' => $applicationid));
      
        $applications_output[] = array(
          'name' => $application->getName(),
          'email' => $application->getEmail(),
          'city' => $application->getCity(),
          'jobLink' => $job_link,
          'detailLink' => $application_detail_link,
          'created' => date('d.m.Y H:i', $application->getCreated()),
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
        ->add('pageurl')
        ->add('employerid')
        ->add('applymail')
        ->add('linkedinid')
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
      
      if ($application->coverpath) {
        $application_output['cover'] = $this->generateUrl('radix_backend_application_attachment', array('accountid' => $accountid, 'applicationid' => $applicationid, 'type' => 'cover'));
      }
      
      if ($application->resumepath) {
        $application_output['resume'] = $this->generateUrl('radix_backend_application_attachment', array('accountid' => $accountid, 'applicationid' => $applicationid, 'type' => 'resume'));
      }

      $carrot['application'] = $application_output;
      
      // parse the job data
      $application_jobid = $application->getJobid();
      
      if ($application_jobid == '-1') {
        $job_title = "Spontaan solliciteren";
      } else {
        $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($application->getJobid());
        if (!$job) {
          $job_title = "-- verwijderde job --";
        } else {
          $job_title = $job->getTitle();
        }
      }
      
      $carrot['job'] = array(
        'title' => $job_title,
      );
      
      // parse the work data
      $work_items = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Work')->findBy(array('applicationid' => $applicationid));
      
      $work_output = array();
      if (is_array($work_items) && count($work_items)) {
        foreach ($work_items as $work) {
        
          $startdate = $work->getStartdate();
          $enddate = $work->getEnddate();
          $period = "";
          
          if ($startdate && !$enddate) {
            $period = $startdate;
          }
          if ($startdate && $enddate) {
            $period = $startdate . " tot " . $enddate;
          }
          if (!$startdate && $enddate) {
            $period = "tot " . $enddate;
          }
        
          $work_output[] = array(
            'employer' => $work->getEmployer(),
            'location' => $work->getLocation(),
            'position' => $work->getPosition(),
            'description' => $work->getDescription(),
            'period' => $period,
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
     * Controller action for application attachment
     **/
    public function applicationAttachmentAction(Request $request, $accountid, $applicationid, $type) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      
    
      // load the application
      $application = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Application')->find($applicationid);
      
      if (!$application) {
        throw $this->createNotFoundException('No application found for this id.');
      }

      $path = $application->getPrivatePath($type);
      if (file_exists($path)) {
				header('Content-Type: ' . mime_content_type($path));
				header('Content-Length: ' . filesize($path));
				
				readfile($path);
				exit();
		  } else {
        exit('This file does not exist.');
      }
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
        ->add('applymail', 'text', array('data' => $carrot['config']->getApplymail()))
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
        $helper = $this->get('radix.helper.facebook');
        
        $action_link = $carrot['config']->getPageurl() . '?id=' . $carrot['config']->getPageid() . '&sk=app_600850943303218';
        
        $params = array(
          'message' => $job->getTitle() . ' #Jobs',
          'link' => 'http://fb.projects.radix-recruitment.be/job-redirect/' . $accountid . '/' . $job->getId(),
          'actions' => "{ 'name': 'view all jobs', 'link': '" . $action_link . "' }",
        );
        
        $helper->post($accountid, $carrot['config']->getPageid(), $params);

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
        ->add('applymail', 'text')
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
     * Controller action for overview of files
     **/
    public function mediaAction(Request $request, $accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

      // get all documents for this account
      $documents = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Document')
        ->findBy(array('accountid' => $accountid));

      $document_output = array();      
      if (is_array($documents) && count($documents)) {
        
        $i = 0;
        $class = "odd";
        foreach ($documents as $document) {
          if (($i % 2) == 0) {
            $class = "even";
          } else {
            $class = "odd";
          }
          $document_output[] = array(
            'path' => "<a target='_blank' href='/" . $document->getWebPath() . "'>" . $document->path . "</a>",
            'type' => $document->getType(),
            'class' => $class,
            'editLink' => "<a href='" . $this->generateUrl('radix_backend_media_edit', array('accountid' => $accountid, 'mediaid' => $document->id)) . "'>bewerken</a>",
            'deleteLink' => "<a href='" . $this->generateUrl('radix_backend_media_delete', array('accountid' => $accountid, 'mediaid' => $document->id)) . "'>verwijderen</a>",
          );
        }
      }

      $carrot['media'] = $document_output;
      $carrot['pageLinks']['addMedia'] = "<a href='" . $this->generateUrl('radix_backend_media_add', array('accountid' => $accountid)) . "' class='add-media'>Bestand toevoegen</a>";      
    
      return $this->render('RadixRecruitmentBundle:Backend:media.html.twig', array('carrot' => $carrot));
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
    
	    $document = new Document();
	    
	    $document->setAccountid($accountid);
	
	    $form = $this->createFormBuilder($document)
	        ->add('file')
	        ->add('type', 'choice', array('choices' => array(
	          'topfront' => 'Top frontpage', 'topdetail' => 'Top jobdetail page', 'bottom' => 'Bottom', 'right' => 'Right', 'thumbnail' => 'Thumbnail',
	        )))
	        ->add('save', 'submit')
	        ->getForm();

	    $form->handleRequest($request);
	
	    if ($form->isValid()) {
	        $em = $this->getDoctrine()->getManager();
	
	        $em->persist($document);
	        $em->flush();

	        $this->get('session')->getFlashBag()->add(
	            'notice',
	            'The document was uploaded!'
	        );
	        
	        return $this->redirect($this->generateUrl('radix_backend_media', array('accountid' => $accountid)));
	
	    }

      return $this->render('RadixRecruitmentBundle:Backend:mediaAdd.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));
    }

 /**********************************************************************************************************/

    /**
     * Controller action for editing a media file
     **/
    public function mediaEditAction(Request $request, $accountid, $mediaid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

      $document = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Document')->find($mediaid);

	    $form = $this->createFormBuilder($document)
	        ->add('file')
	        ->add('type', 'choice', array('choices' => array(
	          'bannerfront' => 'Banner frontpage', 'bannerjob' => 'Banner jobdetail page'
	        )))
	        ->add('save', 'submit')
	        ->getForm();

	    $form->handleRequest($request);
	
	    if ($form->isValid()) {
	        $em = $this->getDoctrine()->getManager();
	
	        $em->persist($document);
	        $em->flush();

	        $this->get('session')->getFlashBag()->add(
	            'notice',
	            'The document was edited!'
	        );
	        
	        return $this->redirect($this->generateUrl('radix_backend_media', array('accountid' => $accountid)));
	
	    }

      return $this->render('RadixRecruitmentBundle:Backend:mediaEdit.html.twig', array('form' => $form->createView(), 'carrot' => $carrot));


    }

 /**********************************************************************************************************/

    /**
     * Controller action for deleting a media file
     **/
    public function mediaDeleteAction(Request $request, $accountid, $mediaid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid, 'backend');

      /**** SERVICES END ****/      

    
      $em = $this->getDoctrine()->getEntityManager();
      $document = $em->getRepository('RadixRecruitmentBundle:Document')->find($mediaid);
      
      if (!$document) {
        throw $this->createNotFoundException('No media file found to delete.');
      }
    
      $em->remove($document);
      $em->flush();
    
       // add flash message
      $this->get('session')->getFlashBag()->add('notice', 'Media file has been deleted.');
   
      return $this->redirect($this->generateUrl('radix_backend_media', array('accountid' => $accountid)));
 
    }



 /**********************************************************************************************************/
   /**
    * Test action
    */
    public function testAction(Request $request, $accountid) {
      $fb_helper = $this->get('radix.helper.facebook');
      
      $fb_helper->testFql();
    }
}