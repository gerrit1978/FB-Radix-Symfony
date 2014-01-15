<?php

namespace Radix\RecruitmentBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Radix\RecruitmentBundle\Entity\Job;
use Radix\RecruitmentBundle\Entity\Config;
use Radix\RecruitmentBundle\Entity\Document;

class FacebookController extends Controller
{
/**********************************************************************************************************/

    /**
     * Controller action for redirecting
     **/
    public function redirectAction(Request $request, $accountid, $nextpage, $id = 0) {
      /**** SERVICES START ****/

      // CARROT service: bootstrap
      $carrot_helper = $this->get('radix.helper.carrot');
      $carrot = $carrot_helper->bootstrap($accountid);

      $facebook_helper = $this->get('radix.helper.facebook');
      $facebook_config = $facebook_helper->getConfig();
      
      /**** SERVICES END ****/      

      // define the concrete next page for the app_data parameter
      $app_data = "/";
      switch ($nextpage) {
        case 'radix_frontend_introduced':
          $app_data .= $accountid .'/frontend/introduced';
          break;
        case 'radix_frontend_social_recruiter':
          $app_data .= $accountid .'/frontend/social-recruiter';
          break;
        case 'radix_backend':
          $app_data .= $accountid .'/backend';
          break;
        case 'radix_frontend':
          $app_data .= $accountid . '/frontend';
          break;
        case 'radix_frontend_job_detail':
          $app_data .= $accountid . '/frontend/job/' . $id;
          break;
        
      }
      
      // normally, there is a "code" parameter in the request
      $code = $request->get('code');
      
      // get the access token
      if ($code) {
        $access_token_url = $facebook_helper->getAccessTokenUrl($accountid, $nextpage, $code, $id);
	      $access_token = substr(file_get_contents($access_token_url), 13);
        $session = new Session();
        $session->set('access_token', $access_token);
        
        $config = $carrot['config'];
        $pageid = $config->getPageid();
        $pagetitle = $config->getPagetitle();
/*         $page_url = "http://www.facebook.com/pages/" . $pagetitle . "/" . $pageid . "?id=" . $pageid . "&sk=app_" . $facebook_config['appId'] . "&app_data=" . $app_data; */
        $page_url = $config->getPageurl() . "?id=" . $pageid . "&sk=app_" . $facebook_config['appId'] . "&app_data=" . $app_data;

        echo("<script>parent.location.href='" . $page_url . "'</script>");

      } else {
        exit('error');
      }
      exit();
      return $this->redirect($this->generateUrl('radix_backend_jobs', array('accountid' => $accountid)));
    }

/**********************************************************************************************************/

}