<?php

namespace Radix\RecruitmentBundle\Helper;

use Symfony\Component\HttpFoundation\Session\Session;

class FacebookHelper {

  private $appid;
  private $secret;

  public function __construct($appid, $secret) {
    
    $this->config = array(
      'appId' => $appid,
      'secret' => $secret,
      'cookie' => TRUE,
    );

  }
  
  public function getConfig() {
    return $this->config;
  }
  
  public function isPageAdmin() {
  
    $facebook = new \Facebook($this->config);
  
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
    return FALSE;
  }
  
  
  public function getProfileData() {
    $facebook = new \Facebook($this->config);
    $user = $facebook->getUser();
    if ($user) {
      try {
        $user_profile = $facebook->api('/me');
        return $user_profile;	      
      } catch (FacebookApiException $e) {
        error_log($e);
        $user = NULL;
        return $user;
      }
    } else {
      return NULL;
    }
  }
  
  public function doRedirect() {

    $facebook = new \Facebook($this->config);
    
    $signed_request = $facebook->getSignedRequest();
    
    if (isset($signed_request['app_data'])) {
      return $signed_request['app_data'];
    }
  }
  
  public function hasAuthorized() {
    $facebook = new \Facebook($this->config);

    $user = $facebook->getUser();
    
    if ($user) {
      try {
        $user_profile = $facebook->api('/me');
        $message = "Goed geauthorized!";
        $ret = array(
          'status' => 'authorized',
          'message' => $message,
          'user_profile' => $user_profile,
        );
      }
      catch (FacebookApiException $e) {
        error_log($e);
        $user = null;
        exit('Gecatcht');
      }
    } else {
      $login_params = array(
        'scope' => 'email,user_work_history,user_education_history',
      );
    
      $loginUrl = $facebook->getLoginUrl($login_params);
      $message = "<a href='" . $loginUrl . "' target='_top'>Log hier eerst in</a>";
      $ret = array(
        'status' => 'not_authorized',
        'message' => $message,
      );
    }
    
    return $ret;
   
    
  }
  
  public function post($params = array()) {
  }
  
  
  public function inCanvas() {
    // THIS SUCKS….
    if (isset($_REQUEST['code'])) {
      return FALSE;
    } else {
      return TRUE;
    }
  }
  
}