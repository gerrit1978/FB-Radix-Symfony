<?php

namespace Radix\RecruitmentBundle\Helper;

use Symfony\Component\HttpFoundation\Session\Session;

class FacebookHelper {

  private $appid;
  private $secret;

  /**
   * Constructor
   * @param string application id
   * @param string application secret
   */
  public function __construct($appid, $secret) {
    
    $this->config = array(
      'appId' => $appid,
      'secret' => $secret,
      'cookie' => TRUE,
    );

  }
  
  /**
   * Returns the configuration parameters
   * 
   * @return array
   */
  public function getConfig() {
    return $this->config;
  }
  
  /**
   * Boots the application on the landing page
   *
   * @return array
   */
  public function boot() {

    $facebook = new \Facebook($this->config);

    $signed_request = $facebook->getSignedRequest();

    $pageid = isset($signed_request['page']['id']) ? $signed_request['page']['id'] : 0;

    $boot = array(
      'pageid' => $pageid,
    );
    
    // put the page id in the session
    $session = new Session();
    $current_session_pageid = $session->get('pageid');
    
    if (!$current_session_pageid || ($current_session_pageid != $pageid)) {
      $session->set('pageid', $pageid);
    }
   
    // add the redirect
    if (isset($signed_request['app_data'])) {
      $boot['redirect'] = $signed_request['app_data'];
    }

    
    return $boot;

  }
  
  /**
   * Returns the page id for this page
   *
   * @return string
   */
  public function getPageId() {
    $facebook = new \Facebook($this->config);
    
    $signed_request = $facebook->getSignedRequest();
    $page_id = $signed_request['page']['id'];
    
    return $page_id;
  }
  
  /**
   * Checks if user is page administrator
   *
   * @return boolean
   */
  public function isPageAdmin() {
 
    $facebook = new \Facebook($this->config);
  
    $session = new Session();
  
    $signed_request = $facebook->getSignedRequest();
    
    $admin = array();
    
    if ($signed_request) {
      if (isset($signed_request['page']['admin']) && $signed_request['page']['admin'] == 1) {
        $admin = array(
          'pageid' => $signed_request['page']['id'],
          'page_admin' => 1,
        );
        $session->set('page_admin', $admin);
        return TRUE;
      } else {
        $session->set('page_admin', $admin);
        return FALSE;
      }
    } else {
      $page_admin = $session->get('page_admin');
      if (isset($page_admin['page_admin'])) {
        return $page_admin['page_admin'];
      } else {
        return FALSE;
      }
    }
    return FALSE;
  }
  
  /**
   * Checks if user has already connected
   *
   * @return boolean
   */
  public function hasConnected() {

    $profile_data = $this->getProfileData();
    
    if ($profile_data == NULL || isset($profile_data->error)) {
      return FALSE;
    } else {
      return TRUE;
    }
  }
  
  /**
   * Returns user profile data
   *
   * @return array
   */
  public function getProfileData() {

    $session = new Session();
    
    $access_token = $session->get('access_token');

    if ($access_token) {
      $graph_url = "https://graph.facebook.com/me?access_token=" . $access_token;
	    $response = $this->curl_get_file_contents($graph_url);
	    $decoded_response = json_decode($response);
	    return $decoded_response;
    } else {
      return NULL;
    }
  }
  
  /**
   * Checks for redirect data if app_data is in query string
   *
   * @return string
   */
  public function doRedirect() {

    $facebook = new \Facebook($this->config);
    
    $signed_request = $facebook->getSignedRequest();
    
    if (isset($signed_request['app_data'])) {
      return $signed_request['app_data'];
    }
  }
  
  /**
   * Checks if user has authorized application
   *
   * @return array
   */
/*
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
*/
  
  public function post($accountid, $facebookid, $params = array()) {

    $redirect_uri = "http://apps.facebook.com/radix-symfony/" . $accountid . "/fb-redirect/radix_backend/0";
    $scope = "manage_pages,publish_stream";

    $access_token = $this->checkAccessToken($scope, $redirect_uri);

    // effe klungelen met access token
    if (strpos($access_token, "&expires")) {
      $pos = strpos($access_token, "&expires");
      $access_token = substr($access_token, 0, $pos);
    }

    $message = isset($params['message']) ? $params['message'] : 'New job online';
    $link = $params['link'];
    $actions = $params['actions'];
 
		$attachment =  array(
	    'access_token'  => $access_token,
	    'message'       => $message,
	    'link'          => $link,
	    'actions'       => $actions,
    );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com/' . $facebookid . '/feed');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close ($ch); 
    
  }
  
  
  public function inCanvas() {
    // THIS SUCKS….
    if (isset($_REQUEST['code'])) {
      return FALSE;
    } else {
      return TRUE;
    }
  }
 
  public function checkAccessToken($scope, $redirect_uri) {

    // split scope
    $scope_split = explode(',', $scope);

    $session = new Session();
    
    $access_token = $session->get('access_token');

    if ($access_token) {
      $graph_url = "https://graph.facebook.com/me/permissions?access_token=" . $access_token;
	    $response = $this->curl_get_file_contents($graph_url);
	    $decoded_response = json_decode($response);
	    
	    // Stap 2a.1: check of access token is nog geldig om de permissies op te halen. Indien neen -> haal nieuw access_token op
			if (isset($decoded_response->error)) {
			
			  // check to see if this is an oAuth error:
			  if ($decoded_response->error->type== "OAuthException") {
			    // Retrieving a valid access token. 
			    $dialog_url= "https://www.facebook.com/dialog/oauth?client_id=" . $this->config['appId'] . "&scope=" . $scope 
			      . "&redirect_uri=" . urlencode($redirect_uri);
			    echo("<script>top.location.href='" . $dialog_url . "'</script>");
			    exit();
			  }
	  		else {
			    echo "other error has happened";
			  }
			} 
	  	else {
			  // access token is geldig om permissies op te halen. Check of de scopes permissie er allemaal tussen zitten
			  $data = isset($decoded_response->data) ? $decoded_response->data : array();
			  $permissions = isset($data[0]) ? $data[0] : '';

			  $valid = TRUE;
        foreach ($scope_split as $item) {
          if (!isset($permissions->$item) || $permissions->$item != 1) {
            $valid = FALSE;
          }
        }
        
        if ($valid) {
			    return $access_token;        
        } else {
			    // Retrieving a valid access token. 
			    $dialog_url= "https://www.facebook.com/dialog/oauth?client_id=" . $this->config['appId'] . "&scope=" . $scope 
			      . "&redirect_uri=" . urlencode($redirect_uri);
			    echo("<script>top.location.href='" . $dialog_url . "'</script>");
			    exit();
        }
			}    
    } else {
			  // check to see if this is an oAuth error:
		    $dialog_url= "https://www.facebook.com/dialog/oauth?client_id=" . $this->config['appId'] . "&scope=" . $scope
			      . "&redirect_uri=" . urlencode($redirect_uri);
		    echo("<script>top.location.href='" . $dialog_url . "'</script>");
        exit();
    }
    
  }
 
  public function getFriendsWorkHistory($accountid, $config) {
    $redirect_uri = "http://apps.facebook.com/radix-symfony/" . $accountid . "/fb-redirect/radix_frontend_introduced/0";
    $scope = "friends_work_history";

    $config_employerid = $config->getEmployerid();

    $access_token = $this->checkAccessToken($scope, $redirect_uri);

		$fql_query_url = 'https://graph.facebook.com/' . 'fql?q=select+uid,+work,+name,+pic_square+FROM+user+WHERE+uid+in+(SELECT+uid2+FROM+friend+WHERE+uid1=me())'
		     . '&access_token=' . $access_token;
	  $fql_query_result = file_get_contents($fql_query_url);
	  $fql_query_obj = json_decode($fql_query_result, true);

	  $data = $fql_query_obj['data'];
	  
	  $connections = array();
	  foreach ($data as $friend) {
	    if (isset($friend['work']) && is_array($friend['work']) && count($friend['work'])) {
	      $uid = $friend['uid'];
	      foreach ($friend['work'] as $work) {
	        if (isset($work['employer']['id'])) {
	          $employer_id = $work['employer']['id'];

	          if ($employer_id == $config_employerid) {
/* 	          if ($employer_id == '106225456082315') { KULEUVEN */
	            $connections[] = array(
	              'name' => $friend['name'],
	              'pic_square' => $friend['pic_square'],
	              'link' => "<span class='send-msg-btn'><a class='send-msg' href='#' id='" . $uid . "'>Send " . $friend['name'] . " a message</a></span>",
	            );
	          }
	        }
	      }
	    }
	  }
	  
	  return $connections;
  }
  
  public function socialRecruiter($accountid, $config) {
  
    $redirect_uri = "http://apps.facebook.com/radix-symfony/" . $accountid . "/fb-redirect/radix_frontend_social_recruiter/0";
    $scope = "publish_stream";
    
    $access_token = $this->checkAccessToken($scope, $redirect_uri);

    $graph_url = 'https://graph.facebook.com/me/feed?message=blablabla&access_token=' . $access_token;
    
    $ch = curl_init();
    
    $fields = array('message' => "Nog even een nieuwe test.");
    
    curl_setopt($ch, CURLOPT_URL, $graph_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
    exit('hier zal een post gebeuren');
    
    $result = curl_exec($ch);
    
  }
  
  public function connect($accountid, $config) {
    $redirect_uri = "http://apps.facebook.com/radix-symfony/" . $accountid . "/fb-redirect/radix_frontend/0";
    $scope = "user_work_history,email,user_education_history,user_location";
    
    $access_token = $this->checkAccessToken($scope, $redirect_uri);
    
    return "hier se";
  }
  
 
  
  /** 
   * Helper function for getting access token URL
   *
   * @return string
   */
  public function getAccessTokenUrl($accountid, $nextpage, $code, $id) {
  
    $url = "http://apps.facebook.com/radix-symfony/" . $accountid . "/fb-redirect/" . $nextpage . '/' . $id;

    $token_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->config['appId'] . '&redirect_uri=' . urlencode($url)
      . '&client_secret=' . $this->config['secret'] . '&code=' . $code;

    return $token_url;
  }
 
	/**
	 * Helper function for curling
	 *
	 * @return misc
	 */
	function curl_get_file_contents($URL) {
	  $c = curl_init();
	  curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($c, CURLOPT_URL, $URL);
	  $contents = curl_exec($c);
	  $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
	  curl_close($c);
	  if ($contents) return $contents;
	  else return FALSE;
	} 
  
}