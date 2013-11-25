<?php

namespace Radix\RecruitmentBundle\Helper;

class FacebookHelper {

  private $facebook;

  public function __construct() {
    $config = array(
      'appId' => '600850943303218',
      'secret' => '41938c8ed1d54041769cb346ffac04d2',
    );

    $this->facebook = new \Facebook($config);

  }
  
  public function isPageAdmin() {
    
    $signed_request = $this->facebook->getSignedRequest();
    
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
    
    $user_id = $this->facebook->getUser();

    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {
        $ret_obj = $this->facebook->api('/me/feed', 'POST',
                                    array(
                                      'link' => 'www.example.com',
                                      'message' => 'Posting with the PHP SDK!'
                                 ));
        echo '<pre>Post ID: ' . $ret_obj['id'] . '</pre>';

        // Give the user a logout link 
        echo '<br /><a href="' . $this->facebook->getLogoutUrl() . '">logout</a>';
      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $this->facebook->getLoginUrl( array(
                       'scope' => 'publish_stream'
                       )); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {

      // No user, so print a link for the user to login
      // To post to a user's wall, we need publish_stream permission
      // We'll use the current URL as the redirect_uri, so we don't
      // need to specify it here.
      $login_url = $this->facebook->getLoginUrl( array( 'scope' => 'publish_stream' ) );
      //$login_url = "http://www.facebook.com/dialog/oauth?app_id=600850943303218&display=popup&redirect_uri=http://apps.facebook.com/radix-symfony&response_type=token%2Csigned_request&scope=user_birthday%2Cuser_interests%2Cuser_work_history%2Cuser_education_history%2Cuser_location%2Cemail%2Cpublish_stream&sdk=joey";
      echo 'Please <a target="_top" href="' . $login_url . '">login.</a>';

    } 
    
    exit('einde');
    
  }
  
  
}

?>