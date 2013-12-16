<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction() {
    
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      
      // FACEBOOK service
      $facebook_helper = $this->get('radix.helper.facebook');
      // boot the application
      $boot = $facebook_helper->boot();

      // check if user is page admin
      $facebook_helper->isPageAdmin();

      $pageid = $boot['pageid'];

      // haal account id op uit database
      $repository = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Config');
      $project = $repository->createQueryBuilder('c')
        ->where('c.pageid = :pageid')
        ->setParameter('pageid', $pageid)
        ->getQuery()
        ->getSingleResult();

      $accountid = $project->getAccountid();


      if (isset($boot['redirect'])) {
        // check if pageid and accountid match!
        $app_data = $boot['redirect'];
        
        // add the slash in front of the redirect parameter if not present
        if (substr($app_data, 0, 1) != '/') {
          $app_data = '/' . $app_data;
        }
        
        $app_data_split = explode('/', $app_data);
        if (isset($app_data_split[1]) && is_numeric($app_data_split[1])) {
          $accountid_redirect = $app_data_split[1];
          if ($accountid_redirect == $accountid) {
            $url = $boot['redirect'];
          } else {
            throw $this->createNotFoundException('No valid redirect found. Something went wrong.');
          }
        } else {
          throw $this->createNotFoundException('No redirect found. Something went wrong.');        
        }
      
        $url = $boot['redirect'];
      } else {
        $url = $this->generateUrl('radix_frontend', array('accountid' => $accountid));
      }
    
      return $this->redirect($url);
    }
}
