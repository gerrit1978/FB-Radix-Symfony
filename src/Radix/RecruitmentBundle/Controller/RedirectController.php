<?php

/*
 * Copyright 2014 - Gerrit Vos & Arne Vanvlasselaer
 * Radix Recruitment
 */

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Radix\RecruitmentBundle\Entity\Config;

class RedirectController extends Controller
{
    // main page action
    public function jobRedirectAction(Request $request, $accountid, $id)
    {
    

      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      /**** SERVICES END ****/

      
      // we make the new URL
      $protocol = "http://";
      if ($request->isSecure()) {
        $protocol = "https://";
      }
      
      $carrot['job'] = array();
      
      // generate the redirect URL
      $pageurl = $carrot['config']->getPageurl();
      $redirecturl = $pageurl . "?id=" . $carrot['config']->getPageid() . "&sk=app_600850943303218&app_data=/" . $accountid . "/frontend/job/" . $id;
      
      // get the job details
      $job = $this->getDoctrine()->getRepository('RadixRecruitmentBundle:Job')->findOneBy(array('id' => $id, 'accountid' => $accountid));
      
      if (!$job) {
        throw $this->createNotFoundException('No job found for this id.');
      }
      
      $title = $job->getTitle();
      
      $carrot['job'] = array(
        'title' => $title,
        'link' => $redirecturl,
        'description' => substr(strip_tags($job->getDescription()), 0, 100) . "…",
      );

      return $this->render('RadixRecruitmentBundle:Frontend:redirect.html.twig', array('carrot' => $carrot));
    }
    
}
