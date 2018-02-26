<?php
/** miniOrange enables user to log in using LDAP credentials.
    Copyright (C) 2015  miniOrange

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
* @package 		miniOrange OAuth
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
/**
This library is miniOrange Authentication Service. 
Contains Request Calls to LDAP Service.

**/
require_once 'class-ldap-auth-response.php';
class Mo_Ldap_Local_Config{
	
	
	function ping_ldap_server($ldap_server_url){
		delete_option('mo_ldap_local_server_url_status');
		delete_option('mo_ldap_local_service_account_status');
		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			return "LDAP_ERROR";
		}
	
		$customer_id = get_option('mo_ldap_local_admin_customer_key');
		$application_name = $_SERVER['SERVER_NAME'];
		$admin_email = get_option('mo_ldap_local_admin_email');
		$app_type = 'WP LDAP for Intranet';
		$request_type = 'Ping LDAP Server';
		
		$ldapconn = ldap_connect($ldap_server_url);
		if ($ldapconn) {
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			// binding anonymously
			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$ldapbind = @ldap_bind($ldapconn);
			if ($ldapbind) {
				add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no');
				return "SUCCESS";
			}
			
		}
		add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no');
		return "ERROR";
	
	}
	
	function ldap_login($username, $password) {

		$username = stripcslashes($username);
		$password = stripcslashes($password);
		
		$authStatus = null;
		
		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			$auth_response = new Mo_Ldap_Auth_Response();
			$auth_response->status = false;
			$auth_response->statusMessage = 'LDAP_ERROR';
			$auth_response->userDn = '';
			return $auth_response;

		}
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			$auth_response = new Mo_Ldap_Auth_Response();
			$auth_response->status = false;
			$auth_response->statusMessage = 'MCRYPT_ERROR';
			$auth_response->userDn = '';
			return $auth_response;
		}
		
		$ldapconn = $this->getConnection();		
		if ($ldapconn) {
			$filter = get_option('mo_ldap_local_search_filter') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_filter')) : '';
			$search_base_string = get_option('mo_ldap_local_search_base') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_base')) : '';
			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';

			$fname_attribute = strtolower(get_option('mo_ldap_local_fname_attribute'));
			$lname_attribute = strtolower(get_option('mo_ldap_local_lname_attribute'));
			$email_attribute = strtolower(get_option('mo_ldap_local_email_attribute'));

			$attr = array($fname_attribute, $lname_attribute, $email_attribute);

			$wp_options = wp_load_alloptions();
			foreach($wp_options as $option=>$value){
				if(strpos($option, "mo_ldap_local_custom_attribute") === false){
					//Do nothing
				} else{
					array_push($attr, $value);
				}
			}
			$filter = str_replace('?', $username, $filter);

			$search_bases = explode(";", $search_base_string);			
			$user_search_result = null;
			$entry = null;
			$info = null;
			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$bind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			$err = ldap_error($ldapconn);
			if(strtolower($err) != 'success'){
				$auth_response = new Mo_Ldap_Auth_Response();
				$auth_response->status = false;
				$auth_response->statusMessage = 'LDAP_NOT_RESPONDING';
				$auth_response->userDn = '';
				return $auth_response;
			}
			for($i = 0 ; $i < sizeof($search_bases) ; $i++){
				if(ldap_search($ldapconn, $search_bases[$i], $filter, $attr))
					$user_search_result = ldap_search($ldapconn, $search_bases[$i], $filter, $attr);
				else{
					$auth_response = new Mo_Ldap_Auth_Response();
					$auth_response->status = false;
					$auth_response->statusMessage = 'USER_NOT_EXIST';
					$auth_response->userDn = '';
					return $auth_response;
				}
				$info = ldap_first_entry($ldapconn, $user_search_result);
				$entry = ldap_get_entries($ldapconn, $user_search_result);
				if($info)
					break;
			}
			if($info){
				$userDn = ldap_get_dn($ldapconn, $info);
			} else{
				$auth_response = new Mo_Ldap_Auth_Response();
				$auth_response->status = false;
				$auth_response->statusMessage = 'USER_NOT_EXIST';
				$auth_response->userDn = '';
				return $auth_response;
			}
			$authentication_response = $this->authenticate($userDn, $password);
			if($authentication_response->statusMessage == 'SUCCESS'){
				$attributes_array = array();
				$profile_attributes = array();

				//Unset fname, lname and email attributes from $attr
				unset($attr[0]);
				unset($attr[1]);
				unset($attr[2]);

				foreach($attr as $attribute){
					/*if(strpos($attribute, "mo_ldap_local_custom_attribute") === false){
						//Do nothing
					}else{*/
						$attributes_array['mo_ldap_local_custom_attribute_'.$attribute] = $entry[0][strtolower($attribute)][0];
					//}
					
				}
				
				$authentication_response->attributeList = $attributes_array;

				//Save email, fname, lname in attributes
				$profile_attributes['mail'] = $entry[0][$email_attribute][0];
				$profile_attributes['fname'] = $entry[0][$fname_attribute][0];
				$profile_attributes['lname'] = $entry[0][$lname_attribute][0];
				
				$authentication_response->profile_attributes = $profile_attributes;
				//var_dump($profile_attributes);exit();

			}
			//var_dump($authentication_response);exit();	
			return $authentication_response;	
			//return $this->authenticate($userDn,$password);
		} else{
			$auth_response = new Mo_Ldap_Auth_Response();
			$auth_response->status = false;
			$auth_response->statusMessage = 'ERROR';
			$auth_response->userDn = '';
			return $auth_response;
		}

	}
	
	function save_ldap_config(){
		if(!Mo_Ldap_Local_Util::is_curl_installed()) {
			return 0;
		}
		
		$url = get_option('mo_ldap_local_host_name') . '/moas/api/ldap/update-config';
		$ch = curl_init($url);
		
		$fields = $this->get_encrypted_config('Save LDAP Service Account Intranet', null);
		$field_string = json_encode($fields);
		
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt( $ch, CURLOPT_TIMEOUT, 1);
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'charset: UTF - 8',
			'Authorization: Basic'
		));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		curl_exec($ch);
		return 1;
	}
		
	/*
	*	Test connection for default config or user config
	*/
	function test_connection() {
		
		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			return json_encode(array("statusCode"=>'LDAP_ERROR','statusMessage'=>'<a target="_blank" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.'));
		} else if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return json_encode(array("statusCode"=>'MCRYPT_ERROR','statusMessage'=>'<a target="_blank" href="http://php.net/manual/en/mcrypt.installation.php">PHP mcrypt extension</a> is not installed or disabled. Please enable it.'));
		}
		
		delete_option('mo_ldap_local_server_url_status');
		delete_option('mo_ldap_local_service_account_status');
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '';
		$pingResult = $this->ping_ldap_server($server_name);
		if($pingResult=='ERROR')
		{
			add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no');
			return json_encode(array("statusCode"=>'PING_ERROR','statusMessage'=>$error . 'Cannot connect to LDAP Server. Make sure you have entered server url in format <b>ldap://server_address:port</b> and if there is a firewall, please open the firewall to allow incoming requests to your LDAP from your wordpress IP and port 389. Also check troubleshooting or contact us using support below.'));
		}
		add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no');

		$ldapconn = $this->getConnection();
		if ($ldapconn) {
			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
				
			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$ldapbind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			// verify binding
			if ($ldapbind) {
				add_option( 'mo_ldap_local_service_account_status', 'VALID', '', 'no');
				return json_encode(array("statusCode"=>'SUCCESS','statusMessage'=>'Connection was established successfully. Please test authentication to verify LDAP User Mapping Configuration.'));
			} else {
				add_option( 'mo_ldap_local_service_account_status', 'INVALID', '', 'no');
				return json_encode(array("statusCode"=>'ERROR','statusMessage'=>$error . 'Invalid service account credentials. Make sure you have entered correct Service Account DN and password.'));
			}
		} else {
			add_option( 'mo_ldap_local_service_account_status', 'INVALID', '', 'no');
			return json_encode(array("statusCode"=>'ERROR','statusMessage'=>$error . 'Invalid service account credentials. Make sure you have entered correct Service Account DN and password.'));
		}
	}
	
	/*
	*	Test authentication for default config or user config
	*/
	function test_authentication($username, $password, $is_default) {

		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			return json_encode(array("statusCode"=>'LDAP_ERROR','statusMessage'=>'<a target="_blank" href="http://php.net/manual/en/ldap.installation.php">PHP LDAP extension</a> is not installed or disabled. Please enable it.'));
		} else if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return json_encode(array("statusCode"=>'MCRYPT_ERROR','statusMessage'=>'<a target="_blank" href="http://php.net/manual/en/mcrypt.installation.php">PHP mcrypt extension</a> is not installed or disabled. Please enable it.'));
		}
		
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '';
		//$pingResult = $this->ping_ldap_server($server_name);
		//if($pingResult=='ERROR')
		if(get_option('mo_ldap_local_server_url_status') && get_option('mo_ldap_local_server_url_status')=='INVALID')
		{
			delete_option('mo_ldap_local_server_url_status');
			delete_option('mo_ldap_local_service_account_status');
			add_option( 'mo_ldap_local_server_url_status', 'INVALID', '', 'no');
			return json_encode(array("statusCode"=>'PING_ERROR','statusMessage'=>$error . 'Cannot connect to LDAP Server. Make sure you have entered server url in format <b>ldap://server_address:port</b> and if there is a firewall, please open the firewall to allow incoming requests to your LDAP from your wordpress IP and port 389. Also check troubleshooting or contact us using support below.'));
		}
		
		
		//Check if request is for default auth
		if(Mo_Ldap_Local_Util::check_empty_or_null($is_default)) {
			delete_option('mo_ldap_local_user_mapping_status');
			delete_option('mo_ldap_local_username_status');
			$auth_response = $this->ldap_login($username, $password);
				if($auth_response->statusMessage == "SUCCESS")
				{
					add_option( 'mo_ldap_local_server_url_status', 'VALID', '', 'no');
					add_option( 'mo_ldap_local_service_account_status', 'VALID', '', 'no');
					add_option( 'mo_ldap_local_user_mapping_status', 'VALID', '', 'no');
					add_option( 'mo_ldap_local_username_status', 'VALID', '', 'no');
					return json_encode(array("statusCode"=>'SUCCESS','statusMessage'=>'You have successfully configured your LDAP settings.'));
				}
				else if($auth_response->statusMessage =="USER_NOT_EXIST")
				{
					add_option( 'mo_ldap_local_user_mapping_status', 'INVALID', '', 'no');
					add_option( 'mo_ldap_local_username_status', 'INVALID', '', 'no');
					return json_encode(array("statusCode"=>'ERROR','statusMessage'=>'Cannot find user <b>'.$username.'</b> in the directory.<br>Possible reasons:<br>1. The <b>search base</b> URL is typed incorrectly. Please verify if that search base is present.<br>2. User is not present in that search base. The user may be present in the directory but in some other tree and you may have entered a tree where this users is not present.<br>3. <b>Search filter</b> is incorrect - User is present in the search base but the username is mapped to a different attribute in the search filter. E.g. you may be logging in with username and may have mapped it to the email attribute. So this wont work. Please make sure that the right attribute is mentioned in the search filter (with which you want the mapping to happen).<br> 4. User is actually not present in the search base. Please make sure that the user is present and test with the right user.'));
				}
				else
				{
					add_option( 'mo_ldap_local_user_mapping_status', 'VALID', '', 'no');
					add_option( 'mo_ldap_local_username_status', 'INVALID', '', 'no');
					return json_encode(array("statusCode"=>'ERROR','statusMessage'=>'Invalid Password. Please check your password and try again.'));
				}
		} else {
			// Default is removed
			return json_encode(array("statusCode"=>'SUCCESS','statusMessage'=>''));
		}
		
	}
	
	
	function getConnection() {
		
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : ''; 
		$ldaprdn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
		$ldappass = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
		
		$ldapconn = ldap_connect($server_name);	
		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option($ldapconn, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}

		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		return $ldapconn;

	}
	
	function authenticate($userDn, $password) {
		
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return false;
		}
		
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : ''; 
						
		$ldapconn = ldap_connect($server_name);
		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option(null, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}
	
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		// binding to ldap server
		
		if(get_option('mo_ldap_local_use_tls'))
			ldap_start_tls($ldapconn);
		$ldapbind = @ldap_bind($ldapconn, $userDn, $password);
		
		// verify binding
		if ($ldapbind) {

			$search_result = ldap_search($ldapconn, $userDn);
			$auth_response = new Mo_Ldap_Auth_Response();
			$auth_response->status = true;
			$auth_response->statusMessage = 'SUCCESS';
			$auth_response->userDn = $userDn;
			return $auth_response;
		} else {
			//$auth_response = new Mo_Ldap_Auth_Response();
			//$auth_response->status = false;
			//$auth_response->userDn = $userDn;
		}
		$auth_response = new Mo_Ldap_Auth_Response();
		$auth_response->status = false;
		$auth_response->statusMessage = 'ERROR';
		$auth_response->userDn = $userDn;
		return $auth_response;
	}
	
	function get_encrypted_config($request_type, $is_default) {
		global $current_user;
		get_currentuserinfo();
		
		$server_name = '';
		$dn = '';
		$search_base = '';
		$search_filter = '';
		$username = $current_user->user_email;
		
		if(Mo_Ldap_Local_Util::check_empty_or_null($is_default)) {
			$server_name = get_option( 'mo_ldap_local_server_url');
			$dn = get_option( 'mo_ldap_local_server_dn');
			$search_base = get_option( 'mo_ldap_local_search_base');
			$search_filter = get_option( 'mo_ldap_local_search_filter');
			$username = get_option('mo_ldap_local_admin_email');
		}
		$customer_id = get_option('mo_ldap_local_admin_customer_key') ? get_option('mo_ldap_local_admin_customer_key') : null;
		
		$fields = array(
			'customerId' => $customer_id,
			'ldapAuditRequest' => array(
				'endUserEmail' => $username,
				'applicationName' => $_SERVER['SERVER_NAME'],
				'appType' => 'WP LDAP Login Plugin',
				'requestType' => $request_type
			),
			'gatewayConfiguration' => array(
				'ldapServer' =>$server_name,
				'bindAccountDN'=>$dn,
				'bindAccountPassword'=>Mo_Ldap_Local_Util::encrypt(''),
				'searchBase'=>$search_base,
				'dnAttribute'=>Mo_Ldap_Local_Util::encrypt(''),
				'ldapSearchFilter'=>$search_filter
			)
			//'onPremGatewayConfiguration'=>false
		);
		
		return $fields;
	}
	
	function get_login_config($encrypted_username, $username, $encrypted_password, $request_type, $is_default) {
		global $current_user;
		get_currentuserinfo();
		
		$customer_id = get_option('mo_ldap_local_admin_customer_key') ? get_option('mo_ldap_local_admin_customer_key') : null;
		
		$fields = array(
			'customerId' => $customer_id,
			'userName' => $encrypted_username,
			'password' => $encrypted_password,
			'ldapAuditRequest' => array(
				'endUserEmail' => $username,
				'applicationName' => $_SERVER['SERVER_NAME'],
				'appType' => 'WP LDAP Login Plugin',
				'requestType' => $request_type
			)
		);
		
		return $fields;
	}
	
	
	function get_user_ldap_info($username) {
	 	if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		//get configuration options
		$server             = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '';
		$ldap_bind_dn       = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
		$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
	
		$search_base   = get_option('mo_ldap_local_search_base') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_base')) : '';
		$search_filter = get_option('mo_ldap_local_search_filter') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_filter')) : '';
			
		$ldap = ldap_connect($server);
	
		if ($ldap) { //connection made! lets bind
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldap);
			$ldapbind = @ldap_bind($ldap, $ldap_bind_dn, $ldap_bind_password);
	
			if ($ldapbind) { //should be bound to the ldap server
				$filter = str_replace('?', $username, $search_filter); //construct the filter
				$sr = ldap_search($ldap, $search_base, $filter); //search the ldap directory
				$info = ldap_get_entries($ldap, $sr); //retrieve our entries
				if (is_null($info)) { // check if we found something
					$info = false;
				}
			} else {
				$info = false;
			}
		} else {
			$info = false; //todo fancy error handling
		}
		if ($info != false) 
			$info = $info[0];
		ldap_close($ldap);
		return $info; //$info is either an array with all the information for the user or false if nothing found
	}

	function modify_user_info_in_ldap($user_info, $custom_attributes){
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : ''; 
		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option( null, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}
			
		$ldapconn = ldap_connect($server_name);
		$distinguished_name = get_user_meta($user_info->ID, 'mo_ldap_user_dn', true);

		if($ldapconn){

			$email_attribute = get_option('mo_ldap_local_email_attribute');
			$fname_attribute = get_option('mo_ldap_local_fname_attribute');
			$lname_attribute = get_option('mo_ldap_local_lname_attribute');

			$user_data = array();
			if(!Mo_Ldap_Local_Util::check_empty_or_null($user_info->first_name))
				$user_data[$fname_attribute] = $user_info->first_name;
			if(!Mo_Ldap_Local_Util::check_empty_or_null($user_info->last_name))
				$user_data[$lname_attribute] = $user_info->last_name;
			if(!Mo_Ldap_Local_Util::check_empty_or_null($user_info->user_email))
				$user_data[$email_attribute] = $user_info->user_email;

			//Modify Custom Attributes
			foreach($custom_attributes as $attribute=>$value){
				if(!Mo_Ldap_Local_Util::check_empty_or_null($value))
					$user_data[$attribute] = $value;
			}

			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

			$ldap_bind_dn       = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';

			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$ldapbind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);

			if($ldapbind)
				ldap_modify($ldapconn, $distinguished_name, $user_data);
			else{
				echo "Could not be done";
				exit();
			}
		}else{
			echo "Could not get connection";
			exit(0);
		}
	}

	function add_user($user_info){
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : ''; 
		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option( null, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}
			
		$ldapconn = ldap_connect($server_name);
		//$distinguished_name = get_user_meta($user_info->ID, 'mo_ldap_user_dn', false)[0];

		if($ldapconn){
			$new_user_location = get_option('mo_ldap_local_new_user_location');
			$user_dn = 'cn=' . $user_info->user_login . ',' . $new_user_location;

			$email_attribute = get_option('mo_ldap_local_email_attribute');
			$fname_attribute = get_option('mo_ldap_local_fname_attribute');
			$lname_attribute = get_option('mo_ldap_local_lname_attribute');
			$objectclass_attribute = get_option('mo_ldap_local_objectclass_attribute');

			$user_data = array();
			$user_data['cn'] = $user_info->user_login;
			$user_data[$fname_attribute] = $user_info->first_name;
			$user_data[$lname_attribute] = $user_info->last_name;
			$user_data[$email_attribute] = $user_info->user_email;
			$user_data['objectClass'] = $objectclass_attribute;


			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
			
			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$ldapbind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			if($ldapbind){
				ldap_add($ldapconn, $user_dn, $user_data);
			}else{
				echo "Could not be done";
				exit();
			}

		}else{
			echo "Could not get connection";
			exit();
		}
	}

	function test_attribute_configuration($username){
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		$server = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '';
		$ldap_bind_dn       = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
		$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
	
		$search_base_string = get_option('mo_ldap_local_search_base') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_base')) : '';
		$search_bases = explode(";", $search_base_string);
		$search_filter = get_option('mo_ldap_local_search_filter') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_filter')) : '';
		$search_filter = str_replace('?', $username, $search_filter);

		$email_attribute = strtolower(get_option('mo_ldap_local_email_attribute'));
		$phone_attribute = strtolower(get_option('mo_ldap_local_phone_attribute'));
		$first_name_attribute = strtolower(get_option('mo_ldap_local_fname_attribute'));
		$last_name_attribute = strtolower(get_option('mo_ldap_local_lname_attribute'));

		$attr = array($email_attribute, $phone_attribute, $first_name_attribute, $last_name_attribute);

		$user = get_user_by('login', $username);
		
		if(get_option('mo_ldap_local_cust') != '0'){
			$wp_options = wp_load_alloptions();
			foreach($wp_options as $option=>$value){
				if(strpos($option, "mo_ldap_local_custom_attribute") === false){
					//Do nothing
				} else{
					array_push($attr, $value);
				}
			}
		}

		$wp_options = wp_load_alloptions();

		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option( null, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}
			
		$ldapconn = ldap_connect($server);

		if ($ldapconn) { 
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$bind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			if($bind){
				/*for($i = 0 ; $i < sizeof($search_bases) ; $i++){
					$user_search_result = ldap_search($ldapconn, $search_bases[$i], $search_filter, $attr);
					//var_dump($user_search_result);exit();
					if($user_search_result){
						//$user_search_result = ldap_search($ldapconn, $search_bases[$i], $search_filter, $attr);
						$info = ldap_first_entry($ldapconn, $user_search_result);
						$entry = ldap_get_entries($ldapconn, $user_search_result);
						$dn = ldap_get_dn($ldapconn, $info);
						break;
					}*/
					//else
					//break;

					for($i = 0 ; $i < sizeof($search_bases) ; $i++){
						if(ldap_search($ldapconn, $search_bases[$i], $search_filter, $attr))
							$user_search_result = ldap_search($ldapconn, $search_bases[$i], $search_filter, $attr);
							$info = ldap_first_entry($ldapconn, $user_search_result);
							$entry = ldap_get_entries($ldapconn, $user_search_result);
							
							if($info){
								$dn = ldap_get_dn($ldapconn, $info);
								break;
							}
						else{
							//Do nothing
						}
						
					}
					
				}
				
				?>
					<style>
						table { border-collapse: collapse; }
						table, th, td { border: 1px solid black;}
						td{padding:5px;}
					</style>
					<h2>Attributes Mapping Test : </h2>
					<table style="width:80%;margin:2%">
						<tr>
							<td style="width:30%;"><b>User DN</b></td>
							<td style="width:80%"><?php echo $dn; ?></td>
						</tr>
						<?php
							foreach($attr as $attribute){
								?><tr>
									<td><b><?php echo $attribute; ?></b></td>
									<td><?php echo $entry[0][$attribute][0]; ?></td>
								</tr>
							<?php
						}
						?>

					</table>

				<?php exit();
			//}
		} else {
			$info = false; 
		}


	}

	function update_password($user_info, $new_password){

		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : ''; 
		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option( null, LDAP_OPT_NETWORK_TIMEOUT, 5);
		}
			
		$ldapconn = ldap_connect($server_name);

		if($ldapconn){

			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
			$distinguished_name = get_user_meta($user_info->ID, 'mo_ldap_user_dn', true);

			if(get_option('mo_ldap_local_use_tls'))
				ldap_start_tls($ldapconn);
			$ldapbind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			if($ldapbind){
				$new_password = "\"" . $new_password . "\"";
				$len = strlen($new_password);
				for ($i = 0; $i < $len; $i++)
				        $new_passw .= "{$new_password{$i}}\000";
				$new_password = $new_passw;
				$userdata["unicodepwd"] = $new_password;
				$result = ldap_mod_replace($ldapconn, $distinguished_name, $userdata);
				if ($result) 
					echo "User modified!" ;
				else 
					echo "There was a problem!";
				exit();

			}else{
				echo "Could not be done";
				exit();
			}

		}

	}
	
	 
	
}?>