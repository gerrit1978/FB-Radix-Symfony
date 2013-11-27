<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Radix\RecruitmentBundle\Entity\Config;

class RedirectController extends Controller
{
    // main page action
    public function jobRedirectAction(Request $request, $accountid, $id)
    {
    
      /* We get the config parameters (XML URL/USER/PASS...) */
      $config = $this->getDoctrine()
        ->getRepository('RadixRecruitmentBundle:Config')
        ->findBy(array('accountid' => $accountid));
 
      if (!$config) {
        throw $this->createNotFoundException('No config found for this accountid.');
      }
      
      // we make the new URL
      $protocol = "http://";
      if ($request->isSecure()) {
        $protocol = "https://";
      }
      
      $url = $protocol . "www.facebook.com/pages/" . $config[0]->getPagetitle() . "/" . $config[0]->getPageid() . "?id=" . $config[0]->getPageid() . "&sk=app_600850943303218"
        . "&app_data=/" . $accountid . "/frontend/job/" . $id;

      return $this->redirect($url);

    }
    
}
