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

class Mo_Ldap_Local_Role_Mapping{

	protected $ldapconn;

	function __construct() {
		$this->ldapconn = $this->getConnection();
	}

	function getConnection() {

		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}

		$server_name = get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '';
		$ldaprdn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
		$ldappass = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';

		if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
			ldap_set_option( null, LDAP_OPT_NETWORK_TIMEOUT, 10);
		}

		$ldapconn = ldap_connect($server_name);
		if ($ldapconn) {
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';
			$bind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			return $ldapconn;
		}
		return null;
	}


	function get_member_of_attribute($username, $password) {

		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			return array("count"=>0);
		}else if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return array("count"=>0);
		}

		$userDn="";
		//$ldapconn = $this->getConnection();
		$ldapconn = $this->ldapconn;
		if ($ldapconn) {
			$filter = get_option('mo_ldap_local_search_filter') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_filter')) : '';
			$search_base_string = get_option('mo_ldap_local_search_base') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_base')) : '';
			$ldap_bind_dn = get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '';
			$ldap_bind_password = get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '';

			$memberOfAtrr = "memberof";
			if(get_option('mo_ldap_local_mapping_memberof_attribute'))
				$memberOfAtrr = strtolower (get_option('mo_ldap_local_mapping_memberof_attribute'));

			$attr = array($memberOfAtrr);
			$filter = str_replace('?', $username, $filter);

			$search_bases = explode(";", $search_base_string);
			$user_search_result = null;
			$info = null;
			//$bind = @ldap_bind($ldapconn, $ldap_bind_dn, $ldap_bind_password);
			for($i = 0 ; $i < sizeof($search_bases) ; $i++){
				if(ldap_search($ldapconn, $search_bases[$i], $filter, $attr))
				{	$user_search_result = ldap_search($ldapconn, $search_bases[$i], $filter, $attr);
					$info = ldap_first_entry($ldapconn, $user_search_result);
					//$info = ldap_get_entries($ldapconn, $user_search_result);
					//print_r($info);exit(0);
					if($info)
						break;
				}
			}
			if($info){
				$userDn = ldap_get_dn($ldapconn, $info);
			}
			//$bind = @ldap_bind($ldapconn, $userDn, $password);
			for($i = 0 ; $i < sizeof($search_bases) ; $i++){
				if(ldap_search($ldapconn, $search_bases[$i], $filter, $attr))
				{
					$user_search_result = ldap_search($ldapconn, $search_bases[$i], $filter, $attr);
					$info = ldap_get_entries($ldapconn, $user_search_result);
					if($info["count"]>0)
					{
						if(isset($info[0][$memberOfAtrr]))
							return $info[0][$memberOfAtrr];
						else
							//return array("count"=>0);
							return $this->get_member_of_attribute_from_groups($userDn);
					}
				}
			}
		}

		return $this->get_member_of_attribute_from_groups($userDn);
	}


	function get_member_of_attribute_from_groups($userDn) {
		if($userDn=="") {
			return array("count"=>0);
		}

		$mapping_count=0;
		if(is_numeric(get_option('mo_ldap_local_role_mapping_count'))) {
			$mapping_count = intval(get_option('mo_ldap_local_role_mapping_count'));
		}
		$membersarray = array("count"=>0);
		$ldapconn = $this->ldapconn;
		for($i = 1 ; $i <= $mapping_count ; $i++){
			$group = get_option( 'mo_ldap_local_mapping_key_'.$i);
			$splittedArray = explode(',', $group, 2);
			if(sizeof($splittedArray)>1){
				$splittedArrayForCn = $splittedArray[0];
				$splittedArrayForCn = explode('=', $splittedArrayForCn);
				if(sizeof($splittedArrayForCn)>1)
					$groupCn = $splittedArrayForCn[1];
				else
					continue;
				$groupBaseDn = $splittedArray[1];
				$filter = "(&(objectClass=*)(cn=".$groupCn."))";
				$search_base = $groupBaseDn;
				$attr = array('member');
				if($ldapconn){
					if(ldap_search($ldapconn, $search_base, $filter, $attr))
					{
						$user_search_result = ldap_search($ldapconn, $search_base, $filter, $attr);
						$info = ldap_get_entries($ldapconn, $user_search_result);
						//print_r($info);
						if($info['count']>0){
							for($j=0; $j<$info['count']; $j++){
								//echo "<br>".$info[$i]['member'][0];
								$matches = preg_grep("/".$userDn."/i", $info[$j]['member']);
								if(count($matches)>0){
									array_push($membersarray,$group);
								}
							}

						}
					}
					//echo ldap_error($ldapconn);
				}
			}
		}
		$membersarray['count'] = sizeof($membersarray);
		return $membersarray; //return array("count"=>0);
	}


	function mo_ldap_local_update_role_mapping($user_id,$member_of_attr ) {
		if($user_id==1) {
			return;
		}

		$roles = 0; //initialize role counter
		$mapping_count=0;
		if(is_numeric(get_option('mo_ldap_local_role_mapping_count'))) {
			$mapping_count = intval(get_option('mo_ldap_local_role_mapping_count'));
		}

		$wpuser = new WP_User($user_id);
		for($i = 1 ; $i <= $mapping_count ; $i++){
			$group = get_option( 'mo_ldap_local_mapping_key_'.$i);
			$matches = preg_grep("/".$group."/i", $member_of_attr);
			if(count($matches)>0){
				$groupMapping = get_option( 'mo_ldap_local_mapping_value_'.$i);
				if($groupMapping)
				{
					if($roles==0)
						$wpuser->set_role('');
					$wpuser->add_role( $groupMapping );
					$roles++; //adds 1 role to the counter
					//return;
				}

			}
		}

		//If no match found set role to default
		if ($roles == 0) {
			if(get_option('mo_ldap_local_mapping_value_default')) {
				$wpuser->set_role( get_option('mo_ldap_local_mapping_value_default') );
			}
		}
	}


	function test_configuration($username){
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			return null;
		}
			$memberOfAtrributesArray = $this->get_member_of_attribute($username,"");
			//get_member_of_attribute_from_groups($userDn);
			$mapping_count=0;
			if(is_numeric(get_option('mo_ldap_local_role_mapping_count'))) {
				$mapping_count = intval(get_option('mo_ldap_local_role_mapping_count'));
			}
			echo "<div style=padding:20px>";
			$flag=0;
			for($i = 1 ; $i <= $mapping_count ; $i++){
				$group = get_option( 'mo_ldap_local_mapping_key_'.$i);
				$matches = preg_grep("/".$group."/i", $memberOfAtrributesArray);
				if(count($matches)>0){
					$groupMapping = get_option( 'mo_ldap_local_mapping_value_'.$i);
					if($groupMapping)
					{
						if($flag==0)
							echo "<span style=color:green;font-size:24px><b>Test Succssful. </b></span><br><br>";
						echo "<li>User <b>".$username."</b> found in group <b>".$group."</b> which matches role <b>".$groupMapping.".</b></li>";
						$flag+=1;
					}

				}
			}
			if($flag==0){
				echo "<span style=color:red;font-size:24px><b>Test Failed. </b></span><br><br>
						<span>User <b>".$username."</b> not found in any group specified in role mapping.</span><br>";
						if(get_option('mo_ldap_local_mapping_value_default')) echo " Default Role <b>".get_option('mo_ldap_local_mapping_value_default')."</b> will be assigned to the User. ";
						echo "<br><br>Please check :
						<li>If you have specified DN for group name e.g.<b>cn=group,dc=domain,dc=com</b></li>
						<li>If you have added users in groups specified in role mapping.</li>";
			}else if($flag==1){
					echo "<br><br><li>Role <b>".$groupMapping."</b> will be assigned to the User.</li>";
			} else{
				echo "<br><br><li><b>Highest Role</b> from above ".$flag." roles will be assigned to the User.</li>";
			}
			echo "</div>";

		 exit();
	}

}
?>
