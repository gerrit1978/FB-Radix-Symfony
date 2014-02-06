<?php

namespace Radix\RecruitmentBundle\Helper;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Radix\RecruitmentBundle\Entity\Watchdog;

class CarrotHelper {
  
  private $doctrine;
  
  public function __construct(Container $container) {
    $this->container = $container;
  }
  
  /* Bootstrap function */
  public function bootstrap($accountid, $type = 'frontend') {

      $carrot = array();
      $carrot['pageLinks'] = array();
      // We get the config parameters
      $config = $this->container->get("doctrine")
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findOneBy(array('accountid' => $accountid));
 
      // If no config object is found -> abort!
      if (!$config) {
        throw new NotFoundHttpException('No config found for this accountid.');
      }

      // Define the router
      $router = $this->container->get("router");

      // Get the normal page id from the config file
      $page_id_from_config = $config->getPageid();
      
      // Call the facebook helper
      $fb_helper = $this->container->get("radix.helper.facebook");

      // Is the current user page admin?
      $isPageAdmin = $fb_helper->isPageAdmin($page_id_from_config);
      
      if ($type == 'frontend') {
	      
	      if ($isPageAdmin) {
	        $carrot['pageLinks']['adminLink'] = "<a href='" . $router->generate('radix_backend', array('accountid' => $accountid)) . "' class='admin-panel'>Admin Panel</a>";
	      }
	      
	      // Did the current user already connect with FB?
	      $hasConnected = $fb_helper->hasConnected();
	      
	      if (!$hasConnected) {
	        $carrot['callToAction'] = array(
            'fbConnect' => "<div class='connect-form'>Wil je graag je Facebook-gegevens gebruiken om te solliciteren? <a class='connect' href='" . $router->generate('radix_frontend_facebook_connect', array('accountid' => $accountid)) . "'>Connecteer dan met Facebook</a></div>",
	        );
	      } else {
	        $carrot['callToAction'] = array(
            'fbConnect' => "<div class='connect-form'>Je bent geconnecteerd met Facebook. Dit betekent dat je je Facebook gegevens kan gebruiken en zo sneller kan solliciteren.</div>",
	        );
	      }
	      
	      // Apply spontaneously
	      $carrot['callToAction']['applySpont'] = "<a class='apply-spont' href='" . $router->generate('radix_frontend_job_apply_manual', array('accountid' => $accountid, 'id' => '-1')) . "'>Spontaan solliciteren</a>";
	      
	      $carrot['pageLinks']['homeLink'] = "<div class='home-link'><span class='button' data-url='" . $router->generate('radix_frontend', array('accountid' => $accountid)) . "'>Naar de startpagina</span></div>";
	      
	      // Render the Introduced link
	      $carrot['introduced'] = "<span class='button introduced' data-url='" . $router->generate('radix_frontend_introduced', array('accountid' => $accountid)) . "'>Laat je introduceren door een vriend.</span>";

	      // Render the social recruiter link -- TODO
	      // $carrot['socialRecruiter'] = "<a class='social-recruiter' href='" . $router->generate('radix_frontend_social_recruiter', array('accountid' => $accountid)) . "'>Word social recruiter</a>";
	      
	      // Get the banners
	      $banners = $this->container->get("doctrine")
	        ->getRepository('RadixRecruitmentBundle:Document')
	        ->findBy(array('accountid' => $accountid));
	      
	      foreach ($banners as $banner) {
	        $carrot['banners'][$banner->getType()] = "<img class='banner " . $banner->getType() . "' src='/" . $banner->getWebPath() . "' />";
	      }
	      
	      // Get the thumbnail
	      $thumbnail = $this->container->get("doctrine")
	        ->getRepository('RadixRecruitmentBundle:Document')
	        ->findOneBy(array('accountid' => $accountid, 'type' => 'thumbnail'));
	      
	      if ($thumbnail) {
	        $carrot['og']['thumbnail'] = "http://fb.projects.radix-recruitment.be/" . $thumbnail->getWebPath();
  	    } else {
  	      $carrot['og']['thumbnail'] = "";
  	    }

      }
      
      if ($type == 'backend') {
      
        // DISALLOW ACCESS IF NECESSARY
        if (!$isPageAdmin) {
          exit('No access for this part of the application.');
        }

        $session = new Session();

        $admin = $session->get('page_admin');        
        $page_id_from_session = $admin['pageid'];
        
        if ($page_id_from_session !== $page_id_from_config) {
          exit('No access for this part of the application.');
        }
        
        // generate the backend links
        $backend_links = array(
          'jobs' => "<a class='tab' href='" . $router->generate('radix_backend_jobs', array('accountid' => $accountid)) . "'>Jobs</a>",
          'applications' => "<a class='tab' href='" . $router->generate('radix_backend_applications', array('accountid' => $accountid)) . "'>Sollicitaties</a>",
/*           'config' => "<a class='tab' href='" . $router->generate('radix_backend_config', array('accountid' => $accountid)) . "'>Configuratie</a>", */
          'media' => "<a class='tab' href='" . $router->generate('radix_backend_media', array('accountid' => $accountid)) . "'>Banners</a>",
        );
        
        $backend_links_output = implode(' &bull; ', $backend_links);
        
        $carrot['pageLinks']['frontendLink'] = "<a href='" . $router->generate('radix_frontend', array('accountid' => $accountid)) . "' class='frontend'>Naar de homepage</a>";
        
        $carrot['pageLinks']['backendLinks'] = "<div class='backend-links'>" . $backend_links_output . "</div>";
      
      }
      
      /* Other Bootstrap stuff comes here */
      $carrot['config'] = $config;
      
      return $carrot;

  }
  
  public function get_page_links($stats) {
    
    $page_links = array(
      'admin' => $this->container->get("controller")->generateUrl('radix_backend', array('accountid' => $accountid)),
    );
    
    return $page_links;
    
  }
  
 
  public function watchdog($accountid, $type, $message) {
	  $watchdog = new Watchdog();
	  $watchdog->setAccountid($accountid);
	  $watchdog->setType($type);
	  $watchdog->setCreated(time());
	  $watchdog->setMessage($message);
	
	  $em = $this->container->get("doctrine")->getManager();
	  $em->persist($watchdog);
	  $em->flush();
  }
  
}