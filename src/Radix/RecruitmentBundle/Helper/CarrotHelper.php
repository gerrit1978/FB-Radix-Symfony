<?php

namespace Radix\RecruitmentBundle\Helper;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class CarrotHelper {
  
  private $doctrine;
  
  public function __construct(Container $container) {
    $this->container = $container;
  }
  
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

      $router = $this->container->get("router");
      
      if ($type == 'frontend') {
      
	      // Call the facebook helper
	      $fb_helper = $this->container->get("radix.helper.facebook");
	
	
	      // Is the current user page admin?
	      $isPageAdmin = $fb_helper->isPageAdmin();
	      
	      if ($isPageAdmin) {
	        $carrot['pageLinks']['adminLink'] = "<a href='" . $router->generate('radix_backend', array('accountid' => $accountid)) . "' class='admin_panel'>Admin Panel</a>";
	      } else {
	        $carrot['pageLinks']['adminLink'] = "";
	      }
	      
	      // Render the other links: get introduced by a friend
	      $carrot['introduced'] = "<a class='introduced' href='" . $router->generate('radix_frontend_introduced', array('accountid' => $accountid)) . "'>Word geïntroduceerd door een vriend.</a>";
      }
      
      if ($type == 'backend') {
       
        $carrot['config'] = $config;

        // generate the backend links
        $backend_links = array(
          'jobs' => "<a class='tab' href='" . $router->generate('radix_backend_jobs', array('accountid' => $accountid)) . "'>Jobs</a>",
          'applications' => "<a class='tab' href='" . $router->generate('radix_backend_applications', array('accountid' => $accountid)) . "'>Applications</a>",
          'config' => "<a class='tab' href='" . $router->generate('radix_backend_config', array('accountid' => $accountid)) . "'>Configuration</a>",
          'media' => "<a class='tab' href='" . $router->generate('radix_backend_media', array('accountid' => $accountid)) . "'>Media</a>",
        );
        
        $backend_links_output = implode(' &bull; ', $backend_links);
        
        $carrot['pageLinks']['frontendLink'] = "<a href='" . $router->generate('radix_frontend', array('accountid' => $accountid)) . "' class='frontend'>Go to frontend</a>";
        
        $carrot['pageLinks']['backendLinks'] = "<div class='backend-links'>" . $backend_links_output . "</div>";
      
      }
      
      /* Other Bootstrap stuff comes here */
      
      return $carrot;

  }
  
  public function get_page_links($stats) {
    
    $page_links = array(
      'admin' => $this->container->get("controller")->generateUrl('radix_backend', array('accountid' => $accountid)),
    );
    
    return $page_links;
    
  }
  
  
  
}