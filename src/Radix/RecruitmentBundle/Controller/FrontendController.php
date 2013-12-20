<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Application;
use Radix\RecruitmentBundle\Entity\Work;
use Radix\RecruitmentBundle\Entity\Education;
use Radix\RecruitmentBundle\Entity\Document;
use Radix\RecruitmentBundle\Entity\Subscriber;

use Radix\RecruitmentBundle\Form\Type\ApplicationType;

class FrontendController extends Controller
{

/**********************************************************************************************************/

    /**
     * Controller action for frontpage
     * - determines if there is a redirect necessary
     * - if no redirect necessary, show blog posts
     **/
    public function indexAction(Request $request, $accountid)
    {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      // FACEBOOK service: redirect
      $helper = $this->get('radix.helper.facebook');
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 
      
      /**** SERVICES END ****/

      /* We get the jobs */
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job');
      $jobs = $repository->createQueryBuilder('j')
        ->where('j.accountid = :accountid')
        ->setParameter('accountid', $accountid)
        ->orderBy('j.created', 'DESC')
        ->getQuery()
        ->getResult();

      $jobs_output = array();
      foreach ($jobs as $job) {
        $subtitle = array();
        if ($location = $job->getLocation()) {
          $subtitle[] = $location;
        }
        if ($industry = $job->getIndustry()) {
          $subtitle[] = $industry;
        }
      
        $jobs_output[] = array(
          'title' => $job->getTitle(),
          'description' => $job->getDescription(),
          'subtitle' => implode(' &bull; ', $subtitle),
          'pagelink' => $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $job->getId())),
          'applink' => 'http://fb.projects.radix-recruitment.be/job-redirect/' . $accountid . '/' . $job->getId(),
          'javascriptlink' => '/' . $accountid . '/frontend/job/' . $job->getId(),
          'onlineSince' => date('d.m.Y', $job->getCreated()),
        );
      }
      $carrot['jobs'] = $jobs_output;
      
      // get the form "Subscribe" TODO - MOVE THIS TO CARROT HELPER
      $subscriber = new Subscriber();
      $subscriber->setAccountid($accountid);
      $subscriber_form = $this->createFormBuilder($subscriber)
        ->add('email', 'email', array('label' => FALSE, 'attr' => array('placeholder' => 'je e-mailadres')))
        ->add('Opslaan', 'submit')
        ->getForm();
      
      $subscriber_form->handleRequest($request);
      
      if ($subscriber_form->isValid()) {
        
        $subscriber->setCreated(time());
        
        // save the config object to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($subscriber);
        $em->flush();

        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'Je werd goed opgenomen in de lijst.');

      }
      
      $carrot['subscriberForm'] = $subscriber_form->createView();
      
      return $this->render('RadixRecruitmentBundle:Frontend:index.html.twig', array('carrot' => $carrot));
    }

/**********************************************************************************************************/
    
    /**
     * Controller action for detail page
     **/
    public function jobDetailAction($accountid, $id) {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      // FACEBOOK service: redirect
      $helper = $this->get('radix.helper.facebook');
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 

      /**** SERVICES END ****/

      /* Is there a banner to be shown? */
      $document = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Document')
        ->findOneBy(array('accountid' => $accountid, 'type' => 'bannerjob'));
      
      if ($document) {
        $path = $document->getWebPath();
        $image = "<img src='/" . $path . "' style='width: 800px; margin-top: 20px; margin-bottom: 20px;' />";
        $carrot['banner'] = $image;
      } else {
        $carrot['banner'] = "";
      }
      
      // get the job details
      $job = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findOneBy(array('id' => $id));
      
      if (!$job) {
        throw $this->createNotFoundException('No job found for this id.');
      }
      
      $subtitle = array();

      if ($location = $job->getLocation()) {
        $subtitle[] = $location;
      }
      if ($industry = $job->getIndustry()) {
        $subtitle[] = $industry;
      }

      $subtitle_output = implode(' &bull; ', $subtitle);
      
      $job_output = array(
        'title' => $job->getTitle(), 
        'subtitle' => $subtitle_output, 
        'description' => $job->getDescription(),
        'applink' => 'http://fb.projects.radix-recruitment.be/job-redirect/' . $accountid . '/' . $job->getId(),
      );
      $carrot['job'] = $job_output;

      $carrot['callToAction']['applyLink'] = "<a class='apply' href='" . $this->generateUrl('radix_frontend_job_apply_manual', array('accountid' => $accountid, 'id' => $id)) . "'>Solliciteer</a>";
      
      $carrot['callToAction']['applyLinkedin'] = '<script src="//platform.linkedin.com/in.js" type="text/javascript">api_key: 77strrnbusgnes</script><script type="IN/Apply" data-companyid="' . $carrot['config']->getLinkedinid() . '" data-jobtitle="' . $job->getTitle() . '" data-email="' . $carrot['config']->getApplymail() . '"></script>';

      // get the form "Subscribe"
      $subscriber = new Subscriber();
      $subscriber->setAccountid($accountid);
      $subscriber_form = $this->createFormBuilder($subscriber)
        ->add('email', 'email', array('label' => FALSE, 'attr' => array('placeholder' => 'je e-mailadres')))
        ->add('Opslaan', 'submit')
        ->getForm();

      $carrot['subscriberForm'] = $subscriber_form->createView();

      
      return $this->render('RadixRecruitmentBundle:Frontend:detail.html.twig', array('carrot' => $carrot));
    }
    
/**********************************************************************************************************/    
    
    /**
     * Controller Action for application page (manual)
     **/
    public function jobApplyAction(Request $request, $accountid, $id) {
      
      $message = "";
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      // FACEBOOK service: redirect
      $helper = $this->get('radix.helper.facebook');
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 
      
      // FACEBOOK service: page admin?
      $isPageAdmin = $helper->isPageAdmin();

      /**** SERVICES END ****/
      
      /* Check if jobid ($id) belongs to a job, or if it's just spontaan solliciteren */
      if ($id === '-1') {
        $job_output['applyTitle'] = "Spontaan solliciteren";
        $job_output['title'] = "Spontaan solliciteren";
      } else {
	      $job = $this->getDoctrine()
	        ->getRepository('RadixRecruitmentBundle:Job')
	        ->findOneBy(array('id' => $id));
	      $job_output = array();
	      if (!$job) {
	        throw $this->createNotFoundException('No job found for this id.');
	      } else {
	        $job_output['applyTitle'] = "Solliciteren voor <a href='" . $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $id)) . "'>" . $job->getTitle() . "</a>";
	        $job_output['title'] = $job->getTitle();
	      }
	    }

      $time = time();


      $application = new Application();
      
      // set necessary ID values
      $application->setAccountid($accountid);
      $application->setJobid($id);
      $application->setCreated($time);

      // get the profile data from Facebook, if possible      
      $helper = $this->get('radix.helper.facebook');
      if ($user_profile = $helper->getProfileData()) {
      
        // get basic stuff
        if (isset($user_profile->name)) {
          $application->setName($user_profile->name);
        }
        
        if (isset($user_profile->email)) {
          $application->setEmail($user_profile->email);
        }
        
        if (isset($user_profile->location) && isset($user_profile->location->name)) {
          $application->setCity($user_profile->location->name);
        }
      
        // get work stuff
        if (isset($user_profile->work)) {
          $fb_work = $user_profile->work;
          if (is_array($fb_work) && count($fb_work)) {
            foreach ($fb_work as $item) {
              $employer = isset($item->employer->name) ? $item->employer->name : '';
              $location = isset($item->location->name) ? $item->location->name : '';
              $position = isset($item->position->name) ? $item->position->name : '';
              $description = isset($item->description) ? $item->description : '';
              $startdate = (isset($item->start_date) && ($item->start_date != '0000-00')) ? $item->start_date : '';
              $enddate = (isset($item->end_date) && ($item->end_date != '0000-00')) ? $item->end_date : '';
                            
              $work = new Work();
              $work->setEmployer($employer);
              $work->setLocation($location);
              $work->setPosition($position);
              $work->setDescription($description);
              $work->setStartdate($startdate);
              $work->setEnddate($enddate);

              $application->getWork()->add($work);
              
            }
          } else {
            $work = new Work();
            $application->getWork()->add($work);
          }
        } else {
          $work = new Work();
          $application->getWork()->add($work);
        }
        
        // get education stuff
        if (isset($user_profile->education)) {
          $fb_education = $user_profile->education;
          if (is_array($fb_education) && count($fb_education)) {
            foreach ($fb_education as $item) {
              
              $school = isset($item->school->name) ? $item->school->name : '';
              $year = isset($item->year->name) ? $item->year->name : '';
              $type = isset($item->type) ? $item->type : '';
              
              $education = new Education();
              $education->setSchool($school);
              $education->setYear($year);
              $education->setType($type);
              
              $application->getEducation()->add($education);
              
            }
          } else {
            $education = new Education();
            $application->getEducation()->add($education);
          }
        } else {
          $education = new Education();
          $application->getEducation()->add($education);
        }
      }

      $form = $this->createForm(new ApplicationType(), $application)
        ->add('resumefile', 'file', array('label' => 'Je CV', 'required' => FALSE))
        ->add('coverfile', 'file', array('label' => 'Je sollicitatiebrief', 'required' => FALSE))
        ->add('Solliciteren', 'submit');
      
      $form->handleRequest($request);
      
      if ($form->isValid()) {
      
        // save the basic application data
        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();
        
        // get the id from this application so we can save the work & education data
        $application_id = $application->getId();

        // get form data        
        $data = $form->getData();

        // iterate over the Work experience items
        $workitems = $data->getWork();
        
        foreach ($workitems as $work) {
          $work->setAccountid($accountid);
          $work->setJobid($id);
          $work->setApplicationid($application_id);
          
          $employer = $work->getEmployer();
          if ($employer) {
	          $em = $this->getDoctrine()->getManager();
	          $em->persist($work);
	          $em->flush();
          }
        }
        
        // and iterate over the Education items
        $educationitems = $data->getEducation();
        
        foreach ($educationitems as $education) {
          $education->setAccountid($accountid);
          $education->setJobid($id);
          $education->setApplicationid($application_id);
          
          $school = $education->getSchool();
          
          if ($school) {
	          $em = $this->getDoctrine()->getManager();
	          $em->persist($education);
	          $em->flush();
	        }
        }
        
        // get the job details - this is needed for sending an email - TODO
        if ($id != '-1') {
	        $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->find($id);
	        
	        $job_title = $job->getTitle();
	        $job_applymail = $job->getApplymail();
	        
	        if (!$job_applymail) {
	          $job_applymail = $carrot['config']->getApplymail();
	        }
	      } else {
	        $job_title = "Spontaan solliciteren";
	        $job_applymail = $carrot['config']->getApplymail();
	      }
        
        $app_url = $carrot['config']->getPageurl() . '?id=' . $carrot['config']->getPageid() . '&sk=app_600850943303218&app_data=/' . $accountid . '/backend/application/' . $application->getId();

        $page_name = $carrot['config']->getPagetitle();
        
        // send an email - TODO: move this to service
		    $message = \Swift_Message::newInstance()
		        ->setSubject('Nieuwe sollicitatie op ' . $page_name)
		        ->setFrom('fb@radix-recruitment.be')
		        ->setTo($job_applymail)
		        ->setBody($this->renderView('RadixRecruitmentBundle:Frontend:email.txt.twig', array(
		          'url' => $app_url,
		          'jobtitle' => $job_title,
		          'pagename' => $page_name,
		        )));
        $this->get('mailer')->send($message);        
        
        
        // add flash message
        $this->get('session')->getFlashBag()->add('notice', 'Je sollicitatie werd goed ontvangen.');
        
        return $this->redirect($this->generateUrl('radix_frontend', array('accountid' => $accountid)));

      }

      $carrot['form'] = $form->createView();
      $carrot['job'] = $job_output;

      return $this->render('RadixRecruitmentBundle:Frontend:apply.html.twig', array('carrot' => $carrot));
    }


/**********************************************************************************************************/    
    
    /**
     * Controller Action for "get introduced by a friend" page (facebook)
     **/
    public function introducedAction(Request $request, $accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      $carrot['pageUrl'] = $carrot['config']->getPageurl();

      // FACEBOOK service: redirect
      $helper = $this->get('radix.helper.facebook');
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 
      
      /**** SERVICES END ****/
      $connections = $helper->getFriendsWorkHistory($accountid, $carrot['config']);
      
      $carrot['connections'] = $connections;

      // get the form "Subscribe"
      $subscriber = new Subscriber();
      $subscriber->setAccountid($accountid);
      $subscriber_form = $this->createFormBuilder($subscriber)
        ->add('email', 'email', array('label' => FALSE, 'attr' => array('placeholder' => 'je e-mailadres')))
        ->add('Opslaan', 'submit')
        ->getForm();

      $carrot['subscriberForm'] = $subscriber_form->createView();

      return $this->render('RadixRecruitmentBundle:Frontend:introduce.html.twig', array('carrot' => $carrot, 'numberOfConnections' => count($connections)));

    }

/**********************************************************************************************************/    
    
    /**
     * Controller Action for "get social recruiter" page (facebook)
     **/
    public function socialRecruiterAction(Request $request, $accountid) {
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      $carrot['banner'] = '';

      // FACEBOOK service: redirect
      $helper = $this->get('radix.helper.facebook');
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 
      
      /**** SERVICES END ****/
      $helper->socialRecruiter($accountid, $carrot['config']);

      exit('socialRecruiterAction is nog niet verder uitgebouwd');
/*      
      return $this->render('RadixRecruitmentBundle:Frontend:socialRecruiter.html.twig', array('scripts' => $scripts, 'carrot' => $carrot));
*/

    }





/**********************************************************************************************************/    
    
    /**
     * Controller Action for connecting with Facebook
     **/
    public function facebookConnectAction(Request $request, $accountid) {

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      // FACEBOOK service: connect details
      $facebook_helper = $this->get('radix.helper.facebook');
      
      $facebook_helper->connect($accountid, $carrot['config']);
      
      /**** SERVICES END ****/
      
      return $this->redirect($this->generateUrl('radix_frontend', array('accountid' => $accountid)));


    }



}
