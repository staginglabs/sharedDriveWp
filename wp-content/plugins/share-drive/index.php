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


  register_rest_route(
    'drive', '/logout/',
    array(
      'methods'  => 'POST',
      'callback' => 'loggout',
    )
  );

register_rest_route(
    'drive', '/changePassword/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'changePassword',
    )
  );

register_rest_route(
    'drive', '/verifyToken/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'verifyToken',
    	)
  );


register_rest_route(
    'drive', '/getUserInfo/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'getUserInfo',
    	)
  );


register_rest_route(
    'drive', '/getUserOrders/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'getUserOrders',
    	)
  );




}



 /**
     * 
     * @param type $requestedToken
     *  Logout User by token
*/
function loggout($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
      if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
			));

         	if(!empty($users)){

         		$user_id=$users[0]->ID;
         			if (delete_user_meta($user_id, '__auth_token_for_shared_drive__') ) {
         				$responsecreds['status']="success";
         				$responsecreds['data']=$responseUser;
         				return $responsecreds;
         			}
         	
         	}else{
	         		$responsecreds['status']="error";
	         		$responsecreds['message']="Invalid Token Please try again";
	         		return $responsecreds;	         		
	         	}  


	    }else{
	         		$responsecreds['status']="error";
	         		$responsecreds['message']="Invalid Token Please try again";
	         		return $responsecreds;	         		
	      	}         		
 }
         



 /**
     * 
     * @param type $request
     *  Checking Login Authorization
*/
function login($request){
          $creds = array();
          header('Access-Control-Allow-Origin: *');
         $creds['user_login'] = $request["username"];
          $creds['user_password'] =  $request["password"];
          $creds['remember'] = true;
          $user = wp_signon( $creds, false );
    ## Invalid User 
    if ( is_wp_error($user) ){
    			$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
   	}else{
        ## Set Token If user is valid
     		$token = getIndentificationString().$creds['user_login'].time();     		
     		$user_endoredata = json_decode(json_encode($user));
     		$user_id = $user_endoredata->data->ID;
     		$responseUser=get_user_meta($user->data->ID);
     		         		
     		update_user_meta( $user_id, '__auth_token_for_shared_drive__', $token);
     		update_user_meta( $user_id, '__auth_token_date_and_time_for_shared_drive__', time());
     		$responseUser=array();
     		$responseUser['user_nicename']=$user->data->user_nicename;
     		$responseUser['user_email']=$user->data->user_email;
     		$responseUser['display_name']=$user->data->display_name;
     		$responseUser['first_name']=$user->data->first_name;
     		$responseUser['last_name']=$user->data->last_name;
     		
     		$responsecreds['status']="success";
         	$responsecreds['data']=$responseUser;
         	$responsecreds['token']=$token;
         	return $responsecreds;

 	 
 	}
 	 
}


 /**
     * 
     * @param type $request
     *  Checking Login verifyToken 
*/
function verifyToken($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){         		

         			$user=get_user_meta($users[0]->data->ID);
         			$responseUser=array();
		     		$responseUser['user_nicename']=$user['nickname'][0];
		     		$responseUser['user_email']=$user['user_email'][0];
		     		$responseUser['display_name']=$user['display_name'][0];
		     		$responseUser['first_name']=$user['first_name'][0];
		     		$responseUser['last_name']=$user['last_name'][0];
		     		$responseUser['token']=$request["token"];

		     		$responsecreds['status']="success";
		     		$responsecreds['data']=$responseUser;
         		return $responsecreds;

         		
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }
         
  
 	 
}




 /**
     * 
     * @param type $request
     *  Checking Login getUserInfo 
*/
function getUserInfo($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){
         		$responseUser=get_user_meta($users[0]->ID);
         		$responsecreds['status']="success";
         		$responsecreds['data']=$responseUser;
         		return $responsecreds;

         		
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         
  
 	 
}



 /**
     * 
     * @param type $request
     *  Change the password of loggedin user using restapi
*/
function changePassword($request){

	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){
		          $creds = array();
		          $new_password=$request["newpassword"];
		          $conform_password=$request["conform_password"];
		     	 wp_set_password( $new_password, $users[0]->ID );



		      }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}


    }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
   
}




/**
     * 
     * @param type $request
     *  Get All Order of user By token
*/
function getUserOrders($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){
         		$responseUser=get_user_meta($users[0]->ID);
         		$customer_orders = get_posts( array(
				    'numberposts' => -1,
				    'meta_key'    => '_customer_user',
				    'meta_value'  => $users[0]->ID,
				    'post_type'   => wc_get_order_types(),
				    
				) );
         		echo "<pre>";print_r($customer_orders);die;

         		$responsecreds['status']="success";
         		$responsecreds['data']=$responseUser;
         		return $responsecreds;

         		
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }
         
  
 	 
}



function getIndentificationString($length = 50) {
    $base64Chars = 'Aqrstuvwadfxyz0%^1234asf56789abcde&^fghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $result = '';
    for ($i = 0; $i < $length; ++$i) {
        $result .= $base64Chars[mt_rand(0, strlen($base64Chars) - 1)];
    }

    return $result;
}