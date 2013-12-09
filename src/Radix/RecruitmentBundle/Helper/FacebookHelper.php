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
 
 
   public function getFriendsWork($accountid) {
     $facebook = new \Facebook($this->config);
     
     $session = new Session();
     
     $access_token = $session->get('access_token');
     
     if (!$access_token) {
     
	     $code = isset($_REQUEST["code"]) ? $_REQUEST['code'] : '';
	     
	     $url = "http://apps.facebook.com/radix-symfony/" . $accountid . "/frontend/introduced";
	     
		  // auth user
		  if (empty($code)){
		    $dialog_url = 'https://www.facebook.com/dialog/oauth?client_id=' . $this->config['appId'] . '&scope=friends_work_history&redirect_uri=' . urlencode($url);
		    echo("<script>parent.location.href='" . $dialog_url . "'</script>");
		  }
		  
		    // get user access_token
	      $token_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->config['appId'] . '&redirect_uri=' . urlencode($url)
	        . '&client_secret=' . $this->config['secret'] . '&code=' . $code;
	      // response is of the format "access_token=AAAC..."
	      $access_token = substr(file_get_contents($token_url), 13);
        $session->set('access_token', $access_token);
     }     
		// run fql query
		$fql_query_url = 'https://graph.facebook.com/' . 'fql?q=select+uid,+work+FROM+user+WHERE+uid+in+(SELECT+uid2+FROM+friend+WHERE+uid1=me())'
		     . '&access_token=' . $access_token;
	  $fql_query_result = file_get_contents($fql_query_url);
	  $fql_query_obj = json_decode($fql_query_result, true);
	     
	
	  $data = $fql_query_obj['data'];
/*
	  print "<pre>";
	  print_r($data);
	  print "</pre>";
	  exit();
*/
	  
	  $connections = array();
	  foreach ($data as $friend) {
	    if (isset($friend['work']) && is_array($friend['work']) && count($friend['work'])) {
	      $uid = $friend['uid'];
	      foreach ($friend['work'] as $work) {
	        if (isset($work['employer']['id'])) {
	          $employer_id = $work['employer']['id'];
	          
	          if ($employer_id == '106225456082315') {
	            $msg_link = "https://www.facebook.com/dialog/send?app_id=" . $this->config['appId'] . "&display=popup&link=http://www.nytimes.com/2011/06/15/arts/people-argue-just-to-win-scholars-assert.html"
	              . "&redirect_uri=https://apps.facebook.com/radix-symfony&to=" . $uid;
	            $connections[] = "<a href='" . $msg_link . "'>Message " . $uid . "</a>";
	          }
	        }
	      }
	    }
	  }
	  print_r($connections);
	  exit();

  }
  
}