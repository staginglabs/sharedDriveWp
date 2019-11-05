<?php
/**
 * Plugin Name: Share Drive
 * Plugin URI: 
 * Description: Rest Api To handle users and there information for Share Drive App.
 * Version: 1.0
 * Author: Simran
 * Author URI:  http://mutewebtechnologies.com/
 */



 /*** Setting Routs for the webservice */

add_action( 'rest_api_init', 'register_api_hooks' );
function register_api_hooks() {
  register_rest_route(
    'drive', '/login/',
    array(
      'methods'  => 'POST',
      'callback' => 'login',
    )
  );

  //   register_rest_route(
  //   'drive', '/changePassword/',
  //   array(
  //     'methods'  => 'POST',
  //     'callback' => 'changePassword',
  //   )
  // );


}

/* Closed  */

 /**
     * 
     * @param type $request
     *  Checking Login Authorization
*/
function login($request){
          $creds = array();
          header('Access-Control-Allow-Origin: *');
         header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
         header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With, Application,Access-Control-Allow-Origin,X-Auth-Token');
          $creds['user_login'] = $request["username"];
          $creds['user_password'] =  $request["password"];
          $creds['remember'] = true;
          $user = wp_signon( $creds, false );
    ## Invalid User 
    if ( is_wp_error($user) ){
    	return $user;
   	}else{
        ## Set Token If user is valid
     		$token = $creds['user_login'].time();
     		$user_endoredata = json_decode(json_encode($user));
     		$user_id = $user_endoredata->data->ID;
     		update_user_meta( $user_id, '__auth_token_for_shared_drive__', $token);
     		return  $user;
 	 
 	}
 	 
}



 /**
     * 
     * @param type $request
     *  Change the password of loggedin user using restapi
*/
function changePassword($request){
          $creds = array();
          $creds['email_id'] = $request["email_id"];
          $creds['previous_password'] =  $request["previous_password"];
          $creds['new_password'] =  $request["new_password"];
          $creds['auth_token'] =  $request["auth_token"];
   
}
add_action( 'after_setup_theme', 'custom_login' );