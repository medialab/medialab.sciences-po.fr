<?php
    /*
    Plugin Name: Active Directory Integration for Intranet sites
    Plugin URI: http://miniorange.com
    Description: Active Directory Integration for Intranet sites plugin provides login to WordPress using credentials stored in your LDAP Server.
    Author: miniorange
    Version: 2.7.43
    Author URI: https://miniorange.com
    */

	require_once 'mo_ldap_pages.php';
	require('mo_ldap_support.php');
	require('class-mo-ldap-customer-setup.php');
	require('class-mo-ldap-utility.php');
	require('class-mo-ldap-config.php');
	require('class-mo-ldap-role-mapping.php');
	error_reporting(E_ERROR);
	class Mo_Ldap_Local_Login{

		function __construct(){
      add_option( 'mo_ldap_local_register_user',1);
			add_option( 'mo_ldap_local_cust', 0);
			add_action('admin_menu', array($this, 'mo_ldap_local_login_widget_menu'));
			add_action('admin_init', array($this, 'login_widget_save_options'));
			add_action('init', array($this, 'test_attribute_configuration'));
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_ldap_local_settings_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_ldap_local_settings_script' ) );
			add_action('parse_request', array($this, 'parse_sso_request'));
			if(get_option('mo_ldap_local_enable_update_ldap') == 1){
				add_action('profile_update', array($this, 'update_profile'));
			}
			remove_action( 'admin_notices', array( $this, 'success_message') );
			remove_action( 'admin_notices', array( $this, 'error_message') );
			add_filter('query_vars', array($this, 'plugin_query_vars'));
			register_deactivation_hook(__FILE__, array( $this, 'mo_ldap_local_deactivate'));
			register_uninstall_hook(__FILE__, array( $this, 'mo_ldap_local_uninstall'));
			add_action( 'login_footer', 'mo_ldap_local_link' );
			add_action('show_user_profile', array($this, 'show_user_profile'));
			if(get_option('mo_ldap_local_enable_login') == 1){
				remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
				add_filter('authenticate', array($this, 'ldap_login'), 100, 3);

				//Add hook to create users in LDAP when new users are created
				//add_action( 'user_register', array($this, 'mo_ldap_add_user'));
			}
			//add_filter( 'show_password_fields', array( $this, false ));
			register_activation_hook( __FILE__, array($this,'mo_ldap_activate')) ;
		}

		function show_user_profile($user){

			$custom_attributes = array();
			$wp_options = wp_load_alloptions();

			?>
			<h3>Extra profile information</h3>

				<table class="form-table">

					<tr>
						<td><b><label for="user_dn">User DN</label></b></td>

						<td>
							<b><?php echo esc_attr( get_the_author_meta( 'mo_ldap_user_dn', $user->ID ) ); ?></b></td>
						</td>
						</tr>
						<?php
						foreach($wp_options as $option=>$value){
							if(strpos($option, "mo_ldap_local_custom_attribute") === false){
								//Do nothing
							}else{
								?>
								<tr>
									<td><b><font color="#FF0000"></font><?php echo $value; ?></b></td>
									<td><input type="text" name="<?php echo $option; ?>" value="<?php echo get_user_meta($user->ID, $option, true); ?>" style="width:61%;" /></td>
								</tr><?php
							}
						}

				?>
				</table>

			<?php
		}

		function update_profile($user_id, $old_user_data){
			$custom_attributes = array();
			foreach($_POST as $post_param=>$value){
				if(strpos($post_param, "mo_ldap_local_custom_attribute") === false){
					//Do nothing
				}else{
					$custom_attributes[str_replace('mo_ldap_local_custom_attribute_', '', $post_param)] = $value;
				}
			}
			//var_dump($custom_attributes);exit();

			$user = get_userdata($user_id);
			$mo_ldap_config = new Mo_Ldap_Local_Config();
			$mo_ldap_config->modify_user_info_in_ldap($user, $custom_attributes);

			//Update user meta
			foreach($custom_attributes as $attribute=>$value){
				update_user_meta($user_id, 'mo_ldap_local_custom_attribute_' . $attribute, $value);
			}
		}

		function ldap_login($user, $username, $password){
			if(empty($username) || empty ($password)){
				//create new error object and add errors to it.
				$error = new WP_Error();

				if(empty($username)){ //No email
					$error->add('empty_username', __('<strong>ERROR</strong>: Email field is empty.'));
				}

				if(empty($password)){ //No password
					$error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
				}
				return $error;
			}


			if(get_option('mo_ldap_local_enable_admin_wp_login')){
				if( username_exists( $username)){
					$user = get_user_by("login",$username);
					if($user  && $this->is_administrator_user($user)){
						if ( wp_check_password( $password, $user->data->user_pass, $user->ID) )
							return $user;
					}
				}
			}

			$mo_ldap_config = new Mo_Ldap_Local_Config();
			//$status = $mo_ldap_config->ldap_login($username, $password);
			$auth_response = $mo_ldap_config->ldap_login($username, $password);

			if($auth_response->statusMessage == 'SUCCESS'){


			  if( username_exists( $username) || email_exists($username)) {
				  $user = get_user_by("login", $username);
				  if(empty($user)){
					$user = get_user_by("email", $username);
				  }
				  if(empty($user)) {
					$error = new WP_Error();
					$error->add('error_fetching_user', __('<strong>ERROR</strong>: Invalid Username/Password combination.'));
					return $error;
				  }
				  if(get_option('mo_ldap_local_enable_role_mapping')) {
				  	  $mo_ldap_role_mapping = new Mo_Ldap_Local_Role_Mapping();
				  	  $member_of_attr = $mo_ldap_role_mapping->get_member_of_attribute($username,$password);
				  	  //$member_of_attr = $mo_ldap_config->get_member_of_attribute($username,$password);
				  	  $mo_ldap_role_mapping->mo_ldap_local_update_role_mapping($user->ID,$member_of_attr);
				  }


				  //Update user password if enabled
				  $fallback_login_enabled = get_option('mo_ldap_local_enable_fallback_login');
				  if($fallback_login_enabled)
				  	wp_set_password($password, $user->ID);

				  //Store distinguishedName in User Meta
				  update_user_meta($user->ID, 'mo_ldap_user_dn', $auth_response->userDn, false);

				  //Update email, phone, fname and lname attributes for user
				  $profile_attributes = $auth_response->profile_attributes;
				  //var_dump($profile_attributes);exit();
				  $user_data['ID'] = $user->ID;
				  if(!empty($profile_attributes['mail']))
					$user_data['user_email'] = $profile_attributes['mail'];
				  if(!empty($profile_attributes['fname']))
					$user_data['first_name'] = $profile_attributes['fname'];
				  if(!empty($profile_attributes['lname']))
					$user_data['last_name'] = $profile_attributes['lname'];


				  wp_update_user($user_data);

				  if(get_option('mo_ldap_local_cust', '1') != '0'){
					  //Store custom attributes in user meta
					  $custom_attributes = $auth_response->attributeList;
					  foreach($custom_attributes as $attribute=>$value){
						update_user_meta($user->ID, $attribute, $value);
					  }
				  }

				  return $user;
			   } else {

					   if(!get_option('mo_ldap_local_register_user')) {
							$error = new WP_Error();
							$error->add('registration_disabled_error', __('<strong>ERROR</strong>: Your Administrator has not enabled Auto Registration. Please contact your Administrator.'));
							return $error;
						}else{
							//create user if not exists
						   $ldap_info = $mo_ldap_config->get_user_ldap_info($username);

						   //Update user password as LDAP password if enabled, else generate new password
						  $fallback_login_enabled = get_option('mo_ldap_local_enable_fallback_login');
						  if($fallback_login_enabled)
						  	$user_password = $password;
						  else
						  	$user_password = wp_generate_password(10, false);

						   $profile_attributes = $auth_response->profile_attributes;

						   $email = $profile_attributes['mail'];
						   $fname = $profile_attributes['fname'];
				  		   $lname = $profile_attributes['lname'];

						   $userdata = array(
								'user_login'  =>  $username,
								'user_email' =>   $email,
								'first_name' =>   $fname,
								'last_name'  =>   $lname,
								'user_pass'   =>  $user_password  // Create user with LDAP password as local password
							);
							$user_id = wp_insert_user( $userdata ) ;

							//On success
							if( !is_wp_error($user_id) ) {
								$user = get_user_by("login",$username);

								  //Store distinguishedName in User Meta
				 				  update_user_meta($user->ID, 'mo_ldap_user_dn', $auth_response->userDn, false);

								  if(get_option('mo_ldap_local_cust', '1') != '0'){
									  //Store custom attributes in user meta
									  $custom_attributes = $auth_response->attributeList;
									  foreach($custom_attributes as $attribute=>$value){
										update_user_meta($user->ID, $attribute, $value);
									  }
								  }

								if(get_option('mo_ldap_local_enable_role_mapping')) {
									$mo_ldap_role_mapping = new Mo_Ldap_Local_Role_Mapping();
									$member_of_attr = $mo_ldap_role_mapping->get_member_of_attribute($username,$password);
									//$member_of_attr = $mo_ldap_config->get_member_of_attribute($username,$password);
				  	  				$mo_ldap_role_mapping->mo_ldap_local_update_role_mapping($user->ID,$member_of_attr);
								}

								return $user;
							}else{
								$error = new WP_Error();
								$error->add('registration_error', __('<strong>ERROR</strong>: There was an error registering your account. Please try again.'));
								return $error;
							}
					}
				}

				wp_redirect( site_url() );
				exit;

			} else if($auth_response->statusMessage == 'LDAP_NOT_RESPONDING'){
				$fallback_login_enabled = get_option('mo_ldap_local_enable_fallback_login');
				if($fallback_login_enabled){
					remove_filter('authenticate', array($this, 'ldap_login'), 20, 3);
					add_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
					$user = wp_authenticate($username, $password);
					return $user;
				}

			}else if($auth_response->statusMessage == 'LDAP_ERROR'){
				$error = new WP_Error();
				$error->add('curl_error', __('<strong>ERROR</strong>: <a target="_blank" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.'));

				return $error;
			} else if($auth_response->statusMessage == 'CURL_ERROR'){
				$error = new WP_Error();
				$error->add('curl_error', __('<strong>ERROR</strong>: <a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
				return $error;
			} else if($auth_response->statusMessage == 'MCRYPT_ERROR'){
				$error = new WP_Error();
				$error->add('mcrypt_error', __('<strong>ERROR</strong>: <a target="_blank" href="http://php.net/manual/en/mcrypt.installation.php">PHP Mcrypt extension</a> is not installed or disabled.'));
				return $error;
			} else {
				$error = new WP_Error();
				$error->add('incorrect_credentials', __('<strong>ERROR</strong>: Invalid username or incorrect password. Please try again.'));
				return $error;
			}
		}

		function mo_ldap_add_user($user_id){
			$user_info = get_userdata($user_id);
			$mo_ldap_config = new Mo_Ldap_Local_Config();
			$mo_ldap_config->add_user($user_info);

		}

		function mo_ldap_local_login_widget_menu(){
			add_menu_page ('LDAP/AD Login for Intranet', 'LDAP/AD Login for Intranet', 'activate_plugins', 'mo_ldap_local_login', array( $this, 'mo_ldap_local_login_widget_options'),plugin_dir_url(__FILE__) . 'includes/images/miniorange_icon.png');
		}

		function mo_ldap_local_login_widget_options(){
			update_option( 'mo_ldap_local_host_name', 'https://auth.miniorange.com' );
			//Setting default configuration
			$default_config = array(
				'server_url' => 'ldap://58.64.132.235:389',
				'service_account_dn' => 'cn=testuser,cn=Users,dc=miniorange,dc=com',
				'admin_password' => 'XXXXXXXX',
				'dn_attribute' => 'distinguishedName',
				'search_base' => 'cn=Users,dc=miniorange,dc=com',
				'search_filter' => '(&(objectClass=*)(cn=?))',
				'test_username' => 'testuser',
				'test_password' => 'password'
			);
			update_option( 'mo_ldap_local_default_config', $default_config );
			mo_ldap_local_settings();
		}

		function login_widget_save_options(){


			if(isset($_POST['option'])){
				if($_POST['option'] == "mo_ldap_local_register_customer") {		//register the customer

					//validate and sanitize
					$email = '';
					$phone = '';
					$password = '';
					$confirmPassword = '';
					$fname = '';
					$lname = '';
					$companyName = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['email'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['password'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['confirmPassword'] ) || Mo_Ldap_Local_Util::check_empty_or_null($_POST['company']) ) {
						update_option( 'mo_ldap_local_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else if( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmPassword'] ) < 6){	//check password is of minimum length 6
						update_option( 'mo_ldap_local_message', 'Choose a password with minimum length 6.');
						$this->show_error_message();
						return;
					} else{
						$email = sanitize_email( $_POST['email'] );
            if(isset($_PHONE['phone']))
						      $phone = sanitize_text_field( $_POST['phone'] );
						$password = sanitize_text_field( $_POST['password'] );
						$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
            if(isset($_POST['fname']))
						      $fname = sanitize_text_field($_POST['fname']);
            if(isset($_POST['lname']))
						      $lname = sanitize_text_field($_POST['lname']);
						$companyName = sanitize_text_field($_POST['company']);
					}

					update_option('mo_ldap_local_admin_email', $email);
					update_option('mo_ldap_local_admin_fname', $fname);
					update_option('mo_ldap_local_admin_lname', $lname);
					update_option('mo_ldap_local_admin_company', $companyName);
					if(!empty($phone))
						update_option( 'mo_ldap_local_admin_phone', $phone );

					if( strcmp( $password, $confirmPassword) == 0 ) {
						update_option( 'mo_ldap_local_password', $password );

						$customer = new Mo_Ldap_Local_Customer();
						$content = json_decode($customer->check_customer(), true);
						if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
							$auth_type = 'EMAIL';
							$content = json_decode($customer->send_otp_token($auth_type, null), true);
							if(strcasecmp($content['status'], 'SUCCESS') == 0) {

								update_option('mo_ldap_local_email_count',1);
								update_option( 'mo_ldap_local_message', 'A One Time Passcode has been sent <b>( 1 )</b> to <b>' . ( get_option('mo_ldap_local_admin_email') ) . '</b>. Please enter the OTP below to verify your email. ');

								update_option('mo_ldap_local_transactionId',$content['txId']);
								update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_SUCCESS');

								$this->show_success_message();
							} else {
								update_option('mo_ldap_local_message','There was an error in sending email. Please click on Resend OTP to try again.');
								update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_FAILURE');
								$this->show_error_message();
							}
						} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0 ){
							update_option('mo_ldap_local_message', $content['statusMessage']);
							update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->show_error_message();
						} else{
							$content = $customer->get_customer_key();
							$customerKey = json_decode($content, true);
							if(json_last_error() == JSON_ERROR_NONE) {
								$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], 'Your account has been retrieved successfully.');
								update_option('mo_ldap_local_password', '');
							} else {
								update_option( 'mo_ldap_local_message', 'You already have an account with miniOrange. Please enter a valid password.');
								update_option('mo_ldap_local_verify_customer', 'true');
								delete_option('mo_ldap_local_new_registration');
								$this->show_error_message();
							}
						}

					} else {
						update_option( 'mo_ldap_local_message', 'Password and Confirm password do not match.');
						delete_option('mo_ldap_local_verify_customer');
						$this->show_error_message();
					}
				}
				else if( $_POST['option'] == "mo_ldap_local_verify_customer" ) {	//login the admin to miniOrange

					//validation and sanitization
					$email = '';
					$password = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['email'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['password'] ) ) {
						update_option( 'mo_ldap_local_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else{
						$email = sanitize_email( $_POST['email'] );
						$password = sanitize_text_field( $_POST['password'] );
					}

					update_option( 'mo_ldap_local_admin_email', $email );
					update_option( 'mo_ldap_local_password', $password );
					$customer = new Mo_Ldap_Local_Customer();
					$content = $customer->get_customer_key();
					$customerKey = json_decode( $content, true );
					if( strcasecmp( $customerKey['apiKey'], 'CURL_ERROR') == 0) {
						update_option('mo_ldap_local_message', $customerKey['token']);
						$this->show_error_message();
					} else if( json_last_error() == JSON_ERROR_NONE ) {
						update_option( 'mo_ldap_local_admin_phone', $customerKey['phone'] );
						$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], 'Your account has been retrieved successfully.');
						update_option('mo_ldap_local_password', '');
					} else {
						update_option( 'mo_ldap_local_message', 'Invalid username or password. Please try again.');
						$this->show_error_message();
					}
					update_option('mo_ldap_local_password', '');
				} else if( $_POST['option'] == "mo_ldap_local_enable" ) {		//enable ldap login
					update_option( 'mo_ldap_local_enable_login', isset($_POST['enable_ldap_login']) ? $_POST['enable_ldap_login'] : 0);
					if(get_option('mo_ldap_local_enable_login')) {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP has been enabled.');
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP has been disabled.');
						$this->show_success_message();
					}
				} else if( $_POST['option'] == "mo_ldap_local_tls_enable" ) {		//enable ldap login
					update_option( 'mo_ldap_local_use_tls', isset($_POST['mo_ldap_local_tls_enable']) ? $_POST['mo_ldap_local_tls_enable'] : 0);
					if(get_option('mo_ldap_local_use_tls')) {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP has been enabled.');
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Login through your LDAP has been disabled.');
						$this->show_error_message();
					}
				} else if( $_POST['option'] == "mo_ldap_local_register_user" ) {		//enable auto registration of users
					update_option( 'mo_ldap_local_register_user', isset($_POST['mo_ldap_local_register_user']) ? $_POST['mo_ldap_local_register_user'] : 0);
					if(get_option('mo_ldap_local_register_user')) {
						update_option( 'mo_ldap_local_message', 'Auto Registering users has been enabled.');
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'Auto Registering users has been disabled.');
						$this->show_success_message();
					}
				}
				else if( $_POST['option'] == "mo_ldap_local_save_config" ) {		//save ldap configuration

					//validation and sanitization
					$server_name = '';
					$dn = '';
					$admin_ldap_password = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['ldap_server'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['dn'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['admin_password'] ) ) {
						update_option( 'mo_ldap_local_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else{
						$server_name = sanitize_text_field( $_POST['ldap_server'] );
						$dn = sanitize_text_field( $_POST['dn'] );
						$admin_ldap_password = sanitize_text_field( $_POST['admin_password'] );
					}

					if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
						update_option( 'mo_ldap_local_message', 'PHP mcrypt extension is not installed or disabled. Please enable it first.');
						$this->show_error_message();
					}else{
						//Encrypting all fields and storing them
						update_option( 'mo_ldap_local_server_url', Mo_Ldap_Local_Util::encrypt($server_name));
						update_option( 'mo_ldap_local_server_dn', Mo_Ldap_Local_Util::encrypt($dn));
						update_option( 'mo_ldap_local_server_password', Mo_Ldap_Local_Util::encrypt($admin_ldap_password));

						delete_option('mo_ldap_local_message');
						$mo_ldap_config = new Mo_Ldap_Local_Config();

						//Save LDAP configuration
						$save_content = $mo_ldap_config->save_ldap_config();
						$message =  'Your configuration has been saved.';
						$status = 'success';

						//Test connection with the LDAP configuration provided. This makes a call to check if connection is established successfully.
						$content = $mo_ldap_config->test_connection();
						$response = json_decode( $content, true );
						if(strcasecmp($response['statusCode'], 'SUCCESS') == 0) {
							add_option( 'mo_ldap_local_message', $message . ' Connection was established successfully. Please configure LDAP User Mapping now.', '', 'no');
							$audit_message = "Test was successful.";
							$this->show_success_message();
						} else if(strcasecmp($response['statusCode'], 'ERROR') == 0) {
							add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no' );
							$audit_message = "Invalid search filter or search base.";
							$this->show_error_message();
						} else if( strcasecmp( $response['statusCode'], 'LDAP_ERROR') == 0) {
							add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no');
							$audit_message = "LDAP extension not installed.";
							$this->show_error_message();
						} else if( strcasecmp( $response['statusCode'], 'MCRYPT_ERROR') == 0) {
							add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no');
							$audit_message = "MCYRPT extension not installed.";
							$this->show_error_message();
						} else if( strcasecmp( $response['statusCode'], 'PING_ERROR') == 0) {
							add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no');
							$audit_message = "Ping server failed ".$server_name;
							$this->show_error_message();
						} else {
							add_option( 'mo_ldap_local_message', $message . ' There was an error in connecting with the current settings. Make sure you have entered server url in format ldap://domain.com:port. Test using Ping LDAP Server.', '', 'no');
							$audit_message = "Invalid configuration.";
							$this->show_error_message();
						}


					}
				} else if( $_POST['option'] == "mo_ldap_local_save_user_mapping" ) {		//save user mapping configuration

					delete_option('mo_ldap_local_user_mapping_status');
					//validation and sanitization
					$dn_attribute = '';
					$search_base = '';
					$search_filter = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['search_base'] )){
						update_option( 'mo_ldap_local_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else{
						$search_base = sanitize_text_field( $_POST['search_base'] );
						if(get_option('mo_ldap_local_cust', '1') == '0'){
							if(strpos($search_base, ';')){
								$pricing_url = add_query_arg( array("tab" => "pricing"), $_SERVER["REQUEST_URI"] );
								$message = 'You have entered multiple search bases. Multiple Search Bases are supported in the <b>Premium version</b> of the plugin. <a href="' . $pricing_url .'">Click here to upgrade</a> ';
								update_option( 'mo_ldap_local_message', $message);
								$this->show_error_message();
								return;
							}
						}
					}

					if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
						update_option( 'mo_ldap_local_message', 'PHP mcrypt extension is not installed or disabled. Please enable it first.');
						$this->show_error_message();
					}else{
						//Encrypting all fields and storing them
						if(!Mo_Ldap_Local_Util::check_empty_or_null($_POST['search_filter'])){
							$search_filter = sanitize_text_field($_POST['search_filter']);
							update_option( 'mo_ldap_local_search_filter', Mo_Ldap_Local_Util::encrypt($search_filter));
						}
						update_option( 'mo_ldap_local_search_base', Mo_Ldap_Local_Util::encrypt($search_base));
						delete_option('mo_ldap_local_message');
						$message =  'LDAP User Mapping Configuration has been saved. Please test authentication to verify LDAP User Mapping Configuration.';
						add_option( 'mo_ldap_local_message', $message , '', 'no');
						$this->show_success_message();
					}
				} else if( $_POST['option'] == "mo_ldap_local_test_auth" ) {		//test authentication with current settings
					$server_name = get_option( 'mo_ldap_local_server_url');
					$dn = get_option( 'mo_ldap_local_server_dn');
					$admin_ldap_password = get_option( 'mo_ldap_local_server_password');
					$search_base = get_option( 'mo_ldap_local_search_base');
					$search_filter = get_option( 'mo_ldap_local_search_filter');

					delete_option('mo_ldap_local_message');

					//validation and sanitization
					$test_username = '';
					$test_password = '';
					//Check if username and password are empty
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['test_username'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['test_password'] ) ) {
						add_option( 'mo_ldap_local_message', 'All the fields are required. Please enter valid entries.', '', 'no');
						$this->show_error_message();
						return;
					}
					//Check if configuration is saved
					else if( Mo_Ldap_Local_Util::check_empty_or_null( $server_name ) || Mo_Ldap_Local_Util::check_empty_or_null( $dn ) || Mo_Ldap_Local_Util::check_empty_or_null( 		$admin_ldap_password ) || Mo_Ldap_Local_Util::check_empty_or_null( $search_base ) || Mo_Ldap_Local_Util::check_empty_or_null( $search_filter ) ) {
						add_option( 'mo_ldap_local_message', 'Please save LDAP Configuration to test authentication.', '', 'no');
						$this->show_error_message();
						return;
					} else{
						$test_username = sanitize_text_field( $_POST['test_username'] );
						$test_password = sanitize_text_field( $_POST['test_password'] );
					}
					//Call to authenticate test
					$mo_ldap_config = new Mo_Ldap_Local_Config();
					$content = $mo_ldap_config->test_authentication($test_username, $test_password, null);
					$response = json_decode( $content, true );

					if(strcasecmp($response['statusCode'], 'SUCCESS') == 0) {
						$role_mapping_url = add_query_arg( array('tab' => 'rolemapping'), $_SERVER['REQUEST_URI'] );
						$message = 'You have successfully configured your LDAP settings.<br>
								You can now do either of two things.<br>
								1. Enable LDAP Login at the top and then <a href="'.wp_logout_url( get_permalink() ).'">Logout</a> from wordpress and login again with your LDAP credentials.<br>
								2. Do role mapping (<a href="'.$role_mapping_url.'">Click here</a>)';
						add_option( 'mo_ldap_local_message', $message, '', 'no');
						$this->show_success_message();
					} else if(strcasecmp($response['statusCode'], 'ERROR') == 0) {
						add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no');
						$this->show_error_message();
					} else if( strcasecmp( $response['statusCode'], 'LDAP_ERROR') == 0) {
						add_option('mo_ldap_local_message', $response['statusMessage'], '', 'no');
						$this->show_error_message();
					} else if( strcasecmp( $response['statusCode'], 'CURL_ERROR') == 0) {
						add_option('mo_ldap_local_message', $response['statusMessage'], '', 'no');
						$this->show_error_message();
					} else if( strcasecmp( $response['statusCode'], 'MCRYPT_ERROR') == 0) {
						add_option( 'mo_ldap_local_message', $response['statusMessage'], '', 'no');
						$this->show_error_message();
					} else if( strcasecmp( $response['statusCode'], 'PING_ERROR') == 0) {
						add_option('mo_ldap_local_message', $response['statusMessage'], '', 'no');
						$this->show_error_message();
					} else {
						add_option( 'mo_ldap_local_message', 'There was an error processing your request. Please verify the Search Base(s) and Search filter. Your user should be present in the Search base defined.', '', 'no');
						$this->show_error_message();
					}
				}
				else if($_POST['option'] == "mo_ldap_local_login_send_query"){
					$query = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['query_email'] ) || Mo_Ldap_Local_Util::check_empty_or_null( $_POST['query'] ) ) {
						update_option( 'mo_ldap_local_message', 'Please submit your query along with email.');
						$this->show_error_message();
						return;
					} else{
						$query = sanitize_text_field( $_POST['query'] );
						$email = sanitize_text_field( $_POST['query_email'] );
						$phone = sanitize_text_field( $_POST['query_phone'] );
						$contact_us = new Mo_Ldap_Local_Customer();
						$submited = json_decode($contact_us->submit_contact_us($email, $phone, $query),true);

						if( strcasecmp( $submited['status'], 'CURL_ERROR') == 0) {
							update_option('mo_ldap_local_message', $submited['statusMessage']);
							$this->show_error_message();
						} else if(json_last_error() == JSON_ERROR_NONE) {
							if ( $submited == false ) {
								update_option('mo_ldap_local_message', 'Your query could not be submitted. Please try again.');
								$this->show_error_message();
							} else {
								update_option('mo_ldap_local_message', 'Thanks for getting in touch! We shall get back to you shortly.');
								$this->show_success_message();
							}
						}

					}
				}
				else if( $_POST['option'] == "mo_ldap_local_resend_otp" ) {			//send OTP to user to verify email

					$customer = new Mo_Ldap_Local_Customer();
					$auth_type = 'EMAIL';
					$content = json_decode($customer->send_otp_token($auth_type, null), true);
					if(strcasecmp($content['status'], 'SUCCESS') == 0) {

							if(get_option('mo_ldap_local_email_count')){
								update_option('mo_ldap_local_email_count',get_option('mo_ldap_local_email_count') + 1);
								update_option( 'mo_ldap_local_message', 'Another One Time Passcode has been sent <b>( ' . get_option('mo_ldap_local_email_count') .' )</b> to <b>' . ( get_option('mo_ldap_local_admin_email') ) . '</b>. Please enter the OTP below to verify your email. ');
							}else{
								update_option( 'mo_ldap_local_message', 'An OTP has been sent to <b>' . ( get_option('mo_ldap_local_admin_email') ) . '</b>. Please enter the OTP below to verify your email. ');
								update_option('mo_ldap_local_email_count',1);
							}

							update_option('mo_ldap_local_transactionId',$content['txId']);
							update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_SUCCESS');
							$this->show_success_message();
					} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
						update_option('mo_ldap_local_message', $content['statusMessage']);
						update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_FAILURE');
						$this->show_error_message();
					} else{
							update_option('mo_ldap_local_message','There was an error in sending email. Please click on Resend OTP to try again.');
							update_option('mo_ldap_local_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->show_error_message();
					}
				}
				else if( $_POST['option'] == "mo_ldap_local_validate_otp"){		//verify OTP entered by user

					//validation and sanitization
					$otp_token = '';
					if( Mo_Ldap_Local_Util::check_empty_or_null( $_POST['otp_token'] ) ) {
						update_option( 'mo_ldap_local_message', 'Please enter a value in otp field.');
						update_option('mo_ldap_local_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
						return;
					} else{
						$otp_token = sanitize_text_field( $_POST['otp_token'] );
					}

					$customer = new Mo_Ldap_Local_Customer();
					$content = json_decode($customer->validate_otp_token(get_option('mo_ldap_local_transactionId'), $otp_token ),true);
					if(strcasecmp($content['status'], 'SUCCESS') == 0) {
						$customer = new Mo_Ldap_Local_Customer();
						$customerKey = json_decode($customer->create_customer(), true);
						delete_option('mo_ldap_local_email_count');
						delete_option('mo_ldap_local_sms_count');
						if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {	//admin already exists in miniOrange
							$content = $customer->get_customer_key();
							$customerKey = json_decode($content, true);
							if(json_last_error() == JSON_ERROR_NONE) {
								$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], 'Your account has been retrieved successfully.');
							} else {
								update_option( 'mo_ldap_local_message', 'You already have an account with miniOrange. Please enter a valid password.');
								update_option('mo_ldap_local_verify_customer', 'true');
								delete_option('mo_ldap_local_new_registration');
								$this->show_error_message();
							}
						} else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) { 	//registration successful
							$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], 'Registration complete!');
						}
						update_option('mo_ldap_local_password', '');
					} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
						update_option('mo_ldap_local_message', $content['statusMessage']);
						update_option('mo_ldap_local_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
					} else{
						update_option( 'mo_ldap_local_message','Invalid one time passcode. Please enter a valid otp.');
						update_option('mo_ldap_local_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
					}
				} else if($_POST['option'] == 'mo_ldap_local_ping_server'){

					delete_option('mo_ldap_local_message');
					//Sanitize form fields
					$ldap_server_url = sanitize_text_field($_POST['ldap_server']);
					$mo_ldap_config = new Mo_Ldap_Local_Config();
					$response = $mo_ldap_config->ping_ldap_server($ldap_server_url);
					if(strcasecmp($response, 'SUCCESS') == 0){
						$status_message = "Successfully contacted LDAP Server. Please configure your Service Account now.";
						$audit_message = "Successfully contacted LDAP Server ".$ldap_server_url;
						add_option('mo_ldap_local_message', $status_message, '', 'no');
						$this->show_success_message();
					} else if(strcasecmp($response, 'LDAP_ERROR') == 0){
						$status_message = "<a target='_blank' href='http://php.net/manual/en/ldap.installation.php'>PHP LDAP extension</a> is not installed or disabled. Please enable it.";
						$audit_message = "LDAP extension not installed for server ".$ldap_server_url;
						add_option('mo_ldap_local_message', 'LDAP Extension is disabled: ' . $status_message, '', 'no');
						$this->show_error_message();
					} else{
						$ldap_ping_url	= str_replace("ldap://", "", $ldap_server_url);
						$ldap_ping_url	= str_replace("ldaps://", "", $ldap_ping_url);
						$ldap_ping_url_array = explode(":", $ldap_ping_url);
						if(isset($ldap_ping_url_array[0]))
							$ldap_ping_url= $ldap_ping_url_array[0];
						$status_message = "Error connecting to LDAP Server. Please check your LDAP server URL <b>".$ldap_server_url."</b>.<br>Possible reasons -<br>1. LDAP URL is typed incorrectly. Type it properly, either with IP address or with fully qualified domain name. Both should work. Eg. <b>ldap://58.64.132.235:389</b> or <b>ldap://ldap.miniorange.com:389</b>.<br>2. LDAP server is unreachable - Open a command prompt and see if you are able to ping the your LDAP server (e.g. type this command on a command prompt <b>ping ".$ldap_ping_url."</b>. If ping is successful then only 'contact ldap server' will work.<br>3. There is a <b>firewall</b> in between - if there is a firewall, please open the firewall to allow incoming requests to your LDAP from your wordpress IP and port 389.";

						$audit_message = "Error connecing server ".$ldap_server_url;
						add_option('mo_ldap_local_message', $status_message, '', 'no');
						$this->show_error_message();
					}


				} else if($_POST['option'] == 'mo_ldap_local_enable_role_mapping'){
					update_option( 'mo_ldap_local_enable_role_mapping', isset($_POST['enable_ldap_role_mapping']) ? $_POST['enable_ldap_role_mapping'] : 0);
					if(get_option('mo_ldap_local_enable_role_mapping')) {
						update_option( 'mo_ldap_local_message', 'LDAP Group to WP role mapping has been enabled.');
						$this->show_success_message();
					} else {
						update_option( 'mo_ldap_local_message', 'LDAP Group to WP role mapping has been disabled.');
						$this->show_success_message();
					}
				} else if($_POST['option'] == 'mo_ldap_local_save_mapping'){
          $max_allowed_mappings = 100;
					$added_mappings_count = 0 ;
					for($i=1;$i<=$max_allowed_mappings;$i++){
						if(isset($_POST['mapping_key_'.$i]) && isset($_POST['mapping_key_'.$i])){
							if($_POST['mapping_key_'.$i]=="")
								continue;
							update_option( 'mo_ldap_local_mapping_key_'.$i, $_POST['mapping_key_'.$i]);
							update_option( 'mo_ldap_local_mapping_value_'.$i, $_POST['mapping_value_'.$i]);
							$added_mappings_count++;
						}else{
							break;
						}
					}
					update_option( 'mo_ldap_local_role_mapping_count', $added_mappings_count);
					if(isset($_POST['mapping_value_default']))
						update_option('mo_ldap_local_mapping_value_default', $_POST['mapping_value_default']);
					if(isset($_POST['mapping_memberof_attribute']))
						update_option('mo_ldap_local_mapping_memberof_attribute', $_POST['mapping_memberof_attribute']);
					$statusMessage='';
					if(!get_option('mo_ldap_local_enable_role_mapping'))
						$statusMessage = ' Please check <b>"Enable Role Mapping"</b> to activate it.';
					update_option( 'mo_ldap_local_message', 'LDAP Group to WP role mapping has been updated.'.$statusMessage);
					$this->show_success_message();
				} else if($_POST['option'] == 'mo_ldap_save_attribute_config'){

					$custom_attributes = array();
					foreach($_POST as $key=>$value){
						if(strpos($key, "mo_ldap_local_custom_attribute") === false){

						} else{
							array_push($custom_attributes, $key);
						}

					}

					update_option( 'mo_ldap_local_enable_update_ldap', isset($_POST['enable_update_ldap']) ? $_POST['enable_update_ldap'] : 0);

					$email_attribute = sanitize_text_field($_POST['mo_ldap_email_attribute']);
					$phone_attribute = sanitize_text_field($_POST['mo_ldap_phone_attribute']);
					$fname_attribute = sanitize_text_field($_POST['mo_ldap_fname_attribute']);
					$lname_attribute = sanitize_text_field($_POST['mo_ldap_lname_attribute']);
					$enabled_attribute = sanitize_text_field($_POST['mo_ldap_user_enabled_attribute']);

					update_option('mo_ldap_local_email_attribute', $email_attribute);
					update_option('mo_ldap_local_phone_attribute', $phone_attribute);
					update_option('mo_ldap_local_fname_attribute', $fname_attribute);
					update_option('mo_ldap_local_lname_attribute', $lname_attribute);
					update_option('mo_ldap_local_user_enabled_attribute', $enabled_attribute);

					foreach($custom_attributes as $attribute){
						if(isset($_POST[$attribute]) and $_POST[$attribute] != ''){
							if(get_option($attribute) == null){
								$attribute_key = 'mo_ldap_local_custom_attribute_' . strtolower(sanitize_text_field($_POST[$attribute]));
							} else{
								$attribute_key = strtolower(sanitize_text_field($_POST[$attribute]));
							}
							update_option($attribute_key, strtolower(sanitize_text_field($_POST[$attribute])));

						}
					}

					update_option( 'mo_ldap_local_message', 'Successfully saved LDAP Attribute Configuration');
					$this->show_success_message();

				} else if($_POST['option'] == 'mo_ldap_user_management_config'){
					$new_user_location = sanitize_text_field($_POST['mo_ldap_new_user_location']);
					$object_class_attribute = sanitize_text_field($_POST['mo_ldap_objectclass_attribute']);

					update_option('mo_ldap_local_new_user_location', $new_user_location);
					update_option('mo_ldap_local_objectclass_attribute', $object_class_attribute);
					update_option( 'mo_ldap_local_message', 'Successfully saved User Management Configuration');
					$this->show_success_message();
				}else if($_POST['option'] == 'mo_ldap_delete_custom_attribute'){
					$custom_attribute_name = sanitize_text_field($_POST['custom_attribute_name']);
					$custom_attribute_key = 'mo_ldap_local_custom_attribute_' . $custom_attribute_name;
					delete_option($custom_attribute_key);
					update_option('mo_ldap_local_message', 'Successfully delete custom attribute: <b>' . $custom_attribute_name . '</b>');
					$this->show_success_message();
				}else if($_POST['option'] == 'user_forgot_password'){
					$admin_email = get_option('mo_ldap_local_admin_email');
					$customer = new Mo_Ldap_Local_Customer();
					$forgot_password_response = json_decode($customer->mo_ldap_local_forgot_password($admin_email));
					if($forgot_password_response->status == 'SUCCESS'){
						$message = 'You password has been reset successfully. Please enter the new password sent to your registered mail here.';
						update_option('mo_ldap_local_message', $message);
						$this->show_success_message();
					}
				} else if($_POST['option'] == 'reset_password'){
					$admin_email = get_option('mo_ldap_local_admin_email');
					$customer = new Mo_Ldap_Local_Customer();
					$forgot_password_response = json_decode($customer->mo_ldap_local_forgot_password($admin_email));
					if($forgot_password_response->status == 'SUCCESS'){
						$message = 'You password has been reset successfully and sent to your registered email. Please check your mailbox.';
						update_option('mo_ldap_local_message', $message);
						$this->show_success_message();
					}
				} else if($_POST['option'] == 'mo_ldap_local_fallback_login'){
					update_option( 'mo_ldap_local_enable_fallback_login', isset($_POST['mo_ldap_local_enable_fallback_login']) ? $_POST['mo_ldap_local_enable_fallback_login'] : 0);
					update_option('mo_ldap_local_message', 'Fallback login using Wordpress password enabled');
					$this->show_success_message();
				}  else if($_POST['option'] == 'mo_ldap_local_enable_admin_wp_login'){
					update_option( 'mo_ldap_local_enable_admin_wp_login', isset($_POST['mo_ldap_local_enable_admin_wp_login']) ? $_POST['mo_ldap_local_enable_admin_wp_login'] : 0);
					if(get_option('mo_ldap_local_enable_admin_wp_login')){
						update_option('mo_ldap_local_message', 'Allow administrators to login with WordPress Credentials is enabled.');
						$this->show_success_message();
					}else{
						update_option('mo_ldap_local_message', 'Allow administrators to login with WordPress Credentials is disabled.');
						$this->show_error_message();
					}
				} else if($_POST['option'] == 'mo_ldap_local_cancel'){
					delete_option('mo_ldap_local_admin_email');
					delete_option('mo_ldap_local_registration_status');
					delete_option('mo_ldap_local_verify_customer');
					delete_option('mo_ldap_local_email_count');
					delete_option('mo_ldap_local_sms_count');
				} else if($_POST['option'] == 'mo_ldap_local_phone_verification'){
					$phone = sanitize_text_field($_POST['phone_number']);
					$phone = str_replace(' ', '', $phone);

					$pattern = "/[\+][0-9]{1,3}[0-9]{10}/";

					if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE)){
						$auth_type = 'SMS';
						$customer = new Mo_Ldap_Local_Customer();
						$send_otp_response = json_decode($customer->send_otp_token($auth_type, $phone));
						if($send_otp_response->status == 'SUCCESS'){
							if(get_option('mo_ldap_local_sms_count') != null){
								update_option('mo_ldap_local_sms_count', get_option('mo_ldap_local_sms_count') + 1);
								$sms_count = get_option('mo_ldap_local_sms_count');
								update_option('mo_ldap_local_message', 'Another One Time Passcode has been sent <b>(' . $sms_count . ')</b> for verification to ' . $phone);
							} else{
								update_option('mo_ldap_local_sms_count', 1);
								update_option('mo_ldap_local_message', 'One Time Passcode has been sent ( <b>1</b> ) for verification to ' . $phone);
							}

							//Save txId
							update_option('mo_ldap_local_transactionId', $send_otp_response->txId);
							$this->show_success_message();
						}
					}else{
						update_option('mo_ldap_local_message', 'Please enter the phone number in the following format: <b>+##country code## ##phone number##');
						$this->show_error_message();
					}
				} else if($_POST['option'] == 'mo_ldap_login_send_query'){
					$email = sanitize_text_field($_POST['query_email']);
					$phone = sanitize_text_field($_POST['query_phone']);
					$query = sanitize_text_field($_POST['query']);
					$customer = new Mo_Ldap_Local_Customer();

					$submit_query_response = $customer->submit_contact_us($email, $phone, $query);
					if($submit_query_response){
						update_option('mo_ldap_local_message', 'Support query successfully sent.<br>In case we dont get back to you, there might be email delivery failures. You can send us email on <a href=mailto:info@miniorange.com><b>info@miniorange.com</b></a> in that case.');
						$this->show_success_message();
					} else{
						update_option('mo_ldap_local_message', 'There was an error sending support query. Please us an email on <a href=mailto:info@miniorange.com><b>info@miniorange.com</b></a>.');
						$this->show_error_message();
					}
				}

			}
		}



		function test_attribute_configuration(){

			if($_REQUEST['option'] != null and $_REQUEST['option'] == 'testattrconfig'){
				$username = $_REQUEST['user'];
				$mo_ldap_config = new Mo_Ldap_Local_Config();
				$mo_ldap_config->test_attribute_configuration($username);
			} else if($_REQUEST['option'] != null and $_REQUEST['option'] == 'testrolemappingconfig'){
				$username = $_REQUEST['user'];
				$mo_ldap_role_mapping = new Mo_Ldap_Local_Role_Mapping();
				$mo_ldap_role_mapping->test_configuration($username);
			}
		}

		/*
		 * Save all required fields on customer registration/retrieval complete.
		 */
		function save_success_customer_config($id, $apiKey, $token, $message) {
			update_option( 'mo_ldap_local_admin_customer_key', $id );
			update_option( 'mo_ldap_local_admin_api_key', $apiKey );
			update_option( 'mo_ldap_local_customer_token', $token );
			update_option('mo_ldap_local_password', '');
			update_option( 'mo_ldap_local_message', $message);
			update_option('mo_ldap_local_role_mapping_count', '0');
			update_option('mo_ldap_local_cust', '0');
			delete_option('mo_ldap_local_verify_customer');
			delete_option('mo_ldap_local_new_registration');
			delete_option('mo_ldap_local_registration_status');
			$this->show_success_message();
		}

		function mo_ldap_local_settings_style() {
			wp_enqueue_style( 'mo_ldap_admin_settings_style', plugins_url('includes/css/style_settings.css', __FILE__));
			wp_enqueue_style( 'mo_ldap_admin_settings_phone_style', plugins_url('includes/css/phone.css', __FILE__));
		}

		function mo_ldap_local_settings_script() {
			wp_enqueue_script( 'mo_ldap_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__ ));
			wp_enqueue_script( 'mo_ldap_admin_settings_script', plugins_url('includes/js/settings_page.js', __FILE__ ), array('jquery'));
		}

		function error_message() {
			$class = "error";
			$message = get_option('mo_ldap_local_message');
			echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
		}

		function success_message() {
			$class = "updated";
			$message = get_option('mo_ldap_local_message');
			echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
		}

		function show_success_message() {
			remove_action( 'admin_notices', array( $this, 'error_message') );
			add_action( 'admin_notices', array( $this, 'success_message') );
		}

		function show_error_message() {
			remove_action( 'admin_notices', array( $this, 'success_message') );
			add_action( 'admin_notices', array( $this, 'error_message') );
		}

		function plugin_query_vars($vars) {
			$vars[] = 'app_name';
			return $vars;
		}

		function parse_sso_request($wp){
			if (array_key_exists('app_name', $wp->query_vars)){
				$redirectUrl = mo_ldap_saml_login($wp->query_vars['app_name']);
				wp_redirect($redirectUrl, 302);
				exit;
			}
		}

		function mo_ldap_activate() {
      ob_clean();
		}


		function mo_ldap_local_uninstall(){
			//Delete Server configuration upon uninstall
			delete_option( 'mo_ldap_local_server_url');
			delete_option( 'mo_ldap_local_server_dn');
			delete_option( 'mo_ldap_local_server_password');
			delete_option('mo_ldap_local_search_filter');
			delete_option('mo_ldap_local_search_base');
			delete_option('mo_ldap_local_role_mapping_count');
		}

		function mo_ldap_local_deactivate() {
			//delete all stored key-value pairs
			if( !Mo_Ldap_Local_Util::check_empty_or_null( get_option('mo_ldap_local_registration_status') ) ) {
				delete_option('mo_ldap_local_admin_email');
			}

			delete_option('mo_ldap_local_host_name');
			delete_option('mo_ldap_local_default_config');
			delete_option('mo_ldap_local_password');
			delete_option('mo_ldap_local_new_registration');
			delete_option('mo_ldap_local_admin_phone');
			delete_option('mo_ldap_local_verify_customer');
			delete_option('mo_ldap_local_admin_customer_key');
			delete_option('mo_ldap_local_admin_api_key');
			delete_option('mo_ldap_local_customer_token');
			delete_option('mo_ldap_local_message');

			delete_option('mo_ldap_local_enable_login');

			delete_option('mo_ldap_local_transactionId');
			delete_option('mo_ldap_local_registration_status');

			delete_option('mo_ldap_local_enable_role_mapping');

		}

		function is_administrator_user( $user ) {
			$userRole = ($user->roles);
			if(!is_null($userRole) && in_array('administrator', $userRole))
				return true;
			else
				return false;
		}

	}

	new Mo_Ldap_Local_Login;
?>
