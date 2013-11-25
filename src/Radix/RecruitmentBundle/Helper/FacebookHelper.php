<?php

namespace Radix\RecruitmentBundle\Helper;

class FacebookHelper {

  public function __construct() {
  }
  
  public function isPageAdmin() {
    $config = array(
      'appId' => '600850943303218',
      'secret' => '41938c8ed1d54041769cb346ffac04d2',
    );

    $facebook = new \Facebook($config);
    
    $signed_request = $facebook->getSignedRequest();
    
    if (isset($signed_request['page']['admin']) && $signed_request['page']['admin'] == 1) {
      $_SESSION['signed_request'] = $signed_request;
      return TRUE;
    } else {
      if (isset($_SESSION['signed_request'])) {
        $signed_request = $_SESSION['signed_request'];
				if (isset($signed_request['page']['admin']) && $signed_request['page']['admin'] == 1) {
				  return TRUE;
				}
			}
    }
/*
    if (isset($_REQUEST['signed_request'])) {
			$signed_request = $_REQUEST["signed_request"];
			list($encoded_sig, $payload) = explode('.', $signed_request, 2);
			
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
			
			if ($data['page']['admin'] == 1) {
			  return TRUE;
			}    
    }
*/
    return FALSE;
  }
  
  public function hasAuthorized() {
    exit('functie aangeroepen');
  }
  
}

?>