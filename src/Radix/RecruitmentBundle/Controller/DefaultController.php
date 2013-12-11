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
        $url = $boot['redirect'];
      } else {
        $url = $this->generateUrl('radix_frontend', array('accountid' => $accountid));
      }
    
      return $this->redirect($url);
    }
}
