<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Application;

class FrontendController extends Controller
{

    /**
     * Controller action for frontpage
     * - determines if there is a redirect necessary
     * - if no redirect necessary, show blog posts
     **/
    public function indexAction(Request $request, $accountid)
    {

      // DO A REDIRECT IF NECESSARY
      $helper = $this->get('radix.helper.facebook');
      
      if ($redirect_url = $helper->doRedirect()) {
        return $this->redirect($redirect_url);
      } 
      
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
          'pagelink' => $this->generateUrl('radix_frontend_job_detail', array('accountid' => $accountid, 'id' => $job->getId())),
          'applink' => 'http://fb.projects.radix-recruitment.be/job-redirect/' . $accountid . '/' . $job->getId(),
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
    
    /**
     * Controller action for detail page
     **/
    public function jobDetailAction($accountid, $id) {
    
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
      
      if (!$job) {
        throw $this->createNotFoundException('No job found for this id.');
      }
      
      $job_output = array('title' => $job->getTitle(), 'description' => $job->getDescription());
      
      $overview_link = $this->generateUrl('radix_frontend', array('accountid' => $accountid));
      
      $apply_manual_link = $this->generateUrl('radix_frontend_job_apply_manual', array('accountid' => $accountid, 'id' => $id));
      
      $apply_facebook_link = $this->generateUrl('radix_frontend_job_apply_facebook', array('accountid' => $accountid, 'id' => $id));
      
    
      return $this->render('RadixRecruitmentBundle:Frontend:detail.html.twig', array(
        'job' => $job_output, 
        'overview_link' => $overview_link, 
        'apply_manual_link' => $apply_manual_link,
        'apply_facebook_link' => $apply_facebook_link
      ));
    }
    
    
    /**
     * Controller Action for application page (manual)
     **/
    public function jobApplyAction(Request $request, $accountid, $id) {
      
      $message = "";
    
      $config = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
      
      if (!$config) {
        throw $this->createNotFoundException('No config found for this accountid.');
      }
      
      $job = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Job')
        ->findOneBy(array('id' => $id));
    
      if (!$job) {
        throw $this->createNotFoundException('No job found for this id.');
      }

      $time = time();
     
      $application = new Application();
      $application->setAccountid($accountid);
      $application->setJobid($id);
      $application->setCreated($time);

      // check if we have access to the user data
      $helper = $this->get('radix.helper.facebook');
      $user_profile = $helper->getProfileData();
      if ($user_profile) {
        $application->setName($user_profile['name']);
        $application->setEmail($user_profile['email']);
      }
      
      $form = $this->createFormBuilder($application)
        ->add('name', 'text')
        ->add('email', 'text')
        ->add('city', 'text')
        ->add('Save', 'submit')
        ->getForm();
    
      $form->handleRequest($request);
    
      if ($form->isValid()) {
      
        // persist object to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();
        
        return $this->redirect($this->generateUrl('radix_frontend', array('accountid' => $accountid)));
      }

      
      $job_output = array('title' => $job->getTitle());

     
    
      return $this->render('RadixRecruitmentBundle:Frontend:apply.html.twig', array('message' => $message, 'job' => $job_output, 'form' => $form->createView()));
    }
    
    
    /**
     * Controller Action for application page (facebook)
     **/
    public function jobApplyFacebookAction(Request $request, $accountid, $id) {
      $helper = $this->get('radix.helper.facebook');
      
      $ret = $helper->hasAuthorized();
      if (isset($ret['status'])) {
        switch ($ret['status']) {
          case 'authorized':
            // check if we need to do a redirect to the facebook page itself
            if (!$in_canvas = $helper->inCanvas()) {
              $url = "http://www.facebook.com/pages/Radix-Recruitment-Demo/196863790499859?id=196863790499859&sk=app_600850943303218&app_data=/".$accountid."/frontend/job/".$id."/apply/facebook";
              return $this->redirect($url);
            } else {
              return $this->redirect($this->generateUrl('radix_frontend_job_apply_manual', array('accountid' => $accountid, 'id' => $id)));
            }
            break;
          case 'not_authorized':
            $message = $ret['message'];
            return $this->render('RadixRecruitmentBundle:Frontend:applyFacebook.html.twig', array('message' => $message));            
            break;
        }
      }
    
    }
    
    public function fbOkAction(Request $request, $accountid) {
    
        return $this->redirect($this->generateUrl('radix_frontend', array('accountid' => $accountid)));
    }
}