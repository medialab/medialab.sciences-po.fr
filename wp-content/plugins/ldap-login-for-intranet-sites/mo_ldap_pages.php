<?php

/*Main function*/
function mo_ldap_local_settings() {
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	} else {
		$active_tab = 'default';
	}

	?>
	<h2>miniOrange LDAP/Active Directory Login for Intranet Sites</h2>
	<?php
		if(!Mo_Ldap_Local_Util::is_curl_installed()) {
			?>

			<div id="help_curl_warning_title" class="mo_ldap_title_panel">
				<p><a target="_blank" style="cursor: pointer;"><font color="#FF0000">Warning: PHP cURL extension is not installed or disabled. <span style="color:blue">Click here</span> for instructions to enable it.</font></a></p>
			</div>
			<div hidden="" id="help_curl_warning_desc" class="mo_ldap_help_desc">
					<ul>
						<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Open php.ini file located under php installation folder.</li>
						<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_curl.dll</b> </li>
						<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
						<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
					</ul>
					For any further queries, please <a href="mailto:info@miniorange.com">contact us</a>.
			</div>

			<?php
		}

		if(!Mo_Ldap_Local_Util::is_extension_installed('ldap')) {
			?>
				<div id="help_ldap_warning_title" class="mo_ldap_title_panel">
				<p><a target="_blank" style="cursor: pointer;"><font color="#FF0000">Warning: PHP LDAP extension is not installed or disabled. <span style="color:blue">Click here</span> for instructions to enable it.</font></a></p>
				</div>
				<div hidden="" id="help_ldap_warning_desc" class="mo_ldap_help_desc">
						<ul>
							<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Ensure that <b>php_ldap.dll</b> exists in you PHP extension directory.</li>
							<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_ldap.dll</b>. </li>
							<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
							<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Add the files <b>libeay32.dll</b> and <b>ssleay32.dll</b> in the root PHP installation directory if not exist.</li>
							<li>Step 5:&nbsp;&nbsp;&nbsp;&nbsp;Copy <b>libsasl.dll</b> from the root PHP installation director to the Apache bin directory if it does not already exist.
							</li>
							<li>Step 6:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
						</ul>
						For any further queries, please <a href="mailto:info@miniorange.com">contact us</a>.
				</div>

			<?php
		}
		if(!Mo_Ldap_Local_Util::is_extension_installed('mcrypt')) {
			?>
			<p><font color="#FF0000">(Warning: <a target="_blank" href="http://php.net/manual/en/mcrypt.installation.php">PHP mcrypt extension</a> is not installed or disabled)</font></p>
			<?php
		}

	?>
	<div class="notice notice-info is-dismissible">
		<h4>If you are looking for Single-Sign-On in addition to authentication with AD/LDAP and do not have any Identity Provider Yet. You can try out <a href="https://idp.miniorange.com" target="_blank">miniOrange On-Premise IdP</a>.</h4>
	</div>
	<div class="mo2f_container">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $active_tab == 'account' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'account'), $_SERVER['REQUEST_URI'] ); ?>">
			<?php if (Mo_Ldap_Local_Util::is_customer_registered())echo "My Account"; else echo "Login / Register"; ?>  </a>
			<a class="nav-tab <?php echo $active_tab == 'default' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'default'), $_SERVER['REQUEST_URI'] ); ?>">LDAP Configuration</a>
			<a class="nav-tab <?php echo $active_tab == 'rolemapping' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'rolemapping'), $_SERVER['REQUEST_URI'] ); ?>">Role Mapping</a>
			<a class="nav-tab <?php echo $active_tab == 'attributemapping' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'attributemapping'), $_SERVER['REQUEST_URI'] ); ?>">Attribute Mapping</a>
			<!--<a class="nav-tab <?php //echo $active_tab == 'usermanagement' ? 'nav-tab-active' : ''; ?>"  href="<?php //echo add_query_arg( array('tab' => 'usermanagement'), $_SERVER['REQUEST_URI'] ); ?>">User Management</a> -->

			<a class="nav-tab <?php echo $active_tab == 'pricing' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Licensing Plans</a>
			<a class="nav-tab <?php echo $active_tab == 'troubleshooting' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'troubleshooting'), $_SERVER['REQUEST_URI'] ); ?>">Troubleshooting</a>

		</h2>
		<table style="width:100%;">
			<tr>
				<td style="width:65%;vertical-align:top;" id="configurationForm">
					<?php
							if($active_tab == "config") {
								mo_ldap_local_default_config_page();
							} else if($active_tab == 'troubleshooting'){
								mo_ldap_local_troubleshooting();
							} else if($active_tab == 'rolemapping'){
								mo_ldap_local_rolemapping();
							} else if($active_tab == 'account'){
								if (get_option ( 'mo_ldap_local_verify_customer' ) == 'true') {
									mo_ldap_local_login_page();
								}
// 								else if (trim ( get_option ( 'mo_ldap_local_admin_email' ) ) != '' && trim ( get_option ( 'mo_ldap_local_admin_api_key' ) ) == '' && get_option ( 'mo_ldap_local_new_registration' ) != 'true') {
// 									mo_ldap_local_login_page();
// 								}
								else if(get_option('mo_ldap_local_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' || get_option('mo_ldap_local_registration_status') == 'MO_OTP_VALIDATION_FAILURE' || get_option('mo_ldap_local_registration_status') == 'MO_OTP_DELIVERED_FAILURE'){
									mo_ldap_local_show_otp_verification();
								}else if (! Mo_Ldap_Local_Util::is_customer_registered()) {
									mo_ldap_local_registration_page();
								}else{
									mo_ldap_local_account_page();
								}
							}else if($active_tab == 'attributemapping'){
									mo_ldap_show_attribute_mapping_page();
							}else if($active_tab == 'usermanagement'){
								    mo_ldap_show_user_management_page();
							}else if($active_tab == 'pricing'){
									show_pricing_page();
							}else {
									mo_ldap_local_configuration_page();
							}
					?>
				</td>
				<td style="vertical-align:top;padding-left:1%;">
					<?php echo mo_ldap_local_support(); ?>
				</td>
			</tr>
		</table>
	</div>
	<?php
}
/*End of main function*/

/* Create Customer function */
function mo_ldap_local_registration_page(){
	update_option ( 'mo_ldap_local_new_registration', 'true' );
	?>

<!--Register with miniOrange-->
<form name="f" method="post" action="">
	<input type="hidden" name="option" value="mo_ldap_local_register_customer" />
	<p>Just complete the short registration below to configure your own LDAP Server. Please enter a valid email id that you have access to. You will be able to move forward after verifying an OTP that we will send to this email.</p>
	<div class="mo_ldap_table_layout" style="min-height: 274px;">
		<h3>Register with miniOrange</h3>
		<div id="panel1">
			<table class="mo_ldap_settings_table">
				<tr>
					<td><b><font color="#FF0000">*</font>Website/Company:</b></td>
					<td><input class="mo_ldap_table_textbox" type="tel" id="company"
						name="company"
						title="Website/Company"
						value="<?php echo $_SERVER['SERVER_NAME']; ?>"
						required placeholder="Company Name" />
					</td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Email:</b></td>
					<td>
					<?php
							$current_user = wp_get_current_user();
							if(get_option('mo_ldap_local_admin_email'))
								$admin_email = get_option('mo_ldap_local_admin_email');
							else
								$admin_email = $current_user->user_email; ?>
					<input class="mo_ldap_table_textbox" type="email" name="email"
						required placeholder="person@example.com"
						value="<?php echo $admin_email;?>" /></td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Password:</b></td>
					<td><input class="mo_ldap_table_textbox" required type="password"
						name="password" placeholder="Choose your password (Min. length 6)" />
					</td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
					<td><input class="mo_ldap_table_textbox" required type="password"
						name="confirmPassword" placeholder="Confirm your password" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Save"
						class="button button-primary button-large" /></td>
				</tr>
			</table>
		</div>
	</div>
</form>
<!--<script>
	jQuery("#phone").intlTelInput();
</script> -->
<?php
}
/* End of Create Customer function */

/* Login for customer*/
function mo_ldap_local_login_page() {
	?>
		<!--Verify password with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_ldap_local_verify_customer" />
			<div class="mo_ldap_table_layout">
				<h3>Login with miniOrange</h3>
				<div id="panel1">
					<table class="mo_ldap_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_ldap_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_ldap_local_admin_email');?>" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_ldap_table_textbox" required type="password"
								name="password" placeholder="Enter your miniOrange password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary button-large" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
								href="#cancel_link">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="#mo_ldap_local_forgot_password_link">Forgot
									your password?</a></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form id="forgot_password_form" method="post" action="">
			<input type="hidden" name="option" value="user_forgot_password" />
		</form>
		<form id="cancel_form" method="post" action="">
			<input type="hidden" name="option" value="mo_ldap_local_cancel" />
		</form>
		<script>

			jQuery('a[href="#cancel_link"]').click(function(){
				jQuery('#cancel_form').submit();
			});

			jQuery('a[href="#mo_ldap_local_forgot_password_link"]').click(function(){
				jQuery('#forgot_password_form').submit();
			});
		</script>
	<?php
}
/* End of Login for customer*/

/* Account for customer*/
function mo_ldap_local_account_page() {
	?>

			<div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; width:98%;height:344px">
				<div>
					<h4>Thank You for registering with miniOrange.</h4>
					<h3>Your Profile</h3>
					<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
						<tr>
							<td style="width:45%; padding: 10px;">Username/Email</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_ldap_local_admin_email')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Customer ID</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_ldap_local_admin_customer_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">API Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_ldap_local_admin_api_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Token Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_ldap_local_customer_token')?></td>
						</tr>
					</table>
					<br/>
					<p><a href="#mo_ldap_local_forgot_password_link">Click here</a> if you forgot your password to your miniOrange account.</p>
				</div>
			</div>

			<form id="forgot_password_form" method="post" action="">
				<input type="hidden" name="option" value="reset_password" />
			</form>

			<script>
				jQuery('a[href="#mo_ldap_local_forgot_password_link"]').click(function(){
					jQuery('#forgot_password_form').submit();
				});
			</script>
			<?php
			if( isset($_POST['option']) && ($_POST['option'] == "mo_ldap_local_verify_customer" ||
					$_POST['option'] == "mo_ldap_local_register_customer") ){ ?>
				<script>
					window.location.href = "<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>";
				</script>
			<?php }
}
/* End of Account for customer*/

function mo_ldap_local_link() {

	?>
	<a href="http://miniorange.com/wordpress-ldap-login" style="display:none;">Login to WordPress using LDAP</a>
	<a href="http://miniorange.com/cloud-identity-broker-service" style="display:none;">Cloud Identity broker service</a>
	<a href="http://miniorange.com/strong_auth" style="display:none;"></a>
	<a href="http://miniorange.com/single-sign-on-sso" style="display:none;"></a>
	<a href="http://miniorange.com/fraud" style="display:none;"></a>
	<?php
}

/* Configure LDAP function */
function mo_ldap_local_configuration_page(){
	$default_config = get_option('mo_ldap_local_default_config');

	$server_url = isset($_POST['ldap_server']) ? stripcslashes($_POST['ldap_server']) :
		( get_option('mo_ldap_local_server_url') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_url')) : '');
	$dn = isset($_POST['dn']) ? stripcslashes($_POST['dn']) :
		(get_option('mo_ldap_local_server_dn') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_dn')) : '');
	$admin_password = isset($_POST['admin_password']) ? stripcslashes($_POST['admin_password']) :
		(get_option('mo_ldap_local_server_password') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_server_password')) : '');
	$search_base = isset($_POST['search_base']) ? stripcslashes($_POST['search_base']) :
		(get_option('mo_ldap_local_search_base') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_base')) : '');
	$search_filter = isset($_POST['search_filter']) ? stripcslashes($_POST['search_filter']) :
		(get_option('mo_ldap_local_search_filter') ? Mo_Ldap_Local_Util::decrypt(get_option('mo_ldap_local_search_filter')) : '');

	// Validdation for each configuration
	$mo_ldap_local_server_url_status="";
	if(get_option( 'mo_ldap_local_server_url_status') && !Mo_Ldap_Local_Util::check_empty_or_null($server_url))
	{
		if(get_option( 'mo_ldap_local_server_url_status')=='VALID')
			$mo_ldap_local_server_url_status="mo_ldap_input_success";
		else if(get_option( 'mo_ldap_local_server_url_status')=='INVALID')
			$mo_ldap_local_server_url_status="mo_ldap_input_error";
	}
	$mo_ldap_local_service_account_status = "";
	if(get_option( 'mo_ldap_local_service_account_status'))
	{
		if(get_option( 'mo_ldap_local_service_account_status')=='VALID')
			$mo_ldap_local_service_account_status="mo_ldap_input_success";
		else if(get_option( 'mo_ldap_local_service_account_status')=='INVALID')
			$mo_ldap_local_service_account_status="mo_ldap_input_error";
	}
	$mo_ldap_local_user_mapping_status = "";
	if(get_option( 'mo_ldap_local_user_mapping_status'))
	{
		if(get_option( 'mo_ldap_local_user_mapping_status')=='VALID')
			$mo_ldap_local_user_mapping_status="mo_ldap_input_success";
		else if(get_option( 'mo_ldap_local_user_mapping_status')=='INVALID')
			$mo_ldap_local_user_mapping_status="mo_ldap_input_error";
	}
	$mo_ldap_local_username_status = "";
	if(get_option( 'mo_ldap_local_username_status'))
	{
		if(get_option( 'mo_ldap_local_username_status')=='VALID')
			$mo_ldap_local_username_status="mo_ldap_input_success";
		else if(get_option( 'mo_ldap_local_username_status')=='INVALID')
			$mo_ldap_local_username_status="mo_ldap_input_error";
		delete_option('mo_ldap_local_username_status');
	}


	?>

		<div class="mo_ldap_small_layout" style="margin-top:0px;">
			<!-- Toggle checkbox -->
		<?php if (!Mo_Ldap_Local_Util::is_customer_registered()) { ?>
				<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'account'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange LDAP Plugin.</div>
		<?php } ?>

			<form name="f" id="enable_login_form" method="post" action="">
				<input type="hidden" name="option" value="mo_ldap_local_enable" />
				<h3>Enable login using LDAP</h3>

				<?php
					$serverUrl = get_option('mo_ldap_local_server_url');
					if(isset($serverUrl) && $serverUrl != ''){?>
						<input type="checkbox" id="enable_ldap_login" name="enable_ldap_login" value="1" <?php checked(get_option('mo_ldap_local_enable_login') == 1);?> />Enable LDAP login
				<?php } else{?>
						<input type="checkbox" id="enable_ldap_login" name="enable_ldap_login" value="1" <?php checked(get_option('mo_ldap_local_enable_login') == 1);?> disabled />Enable LDAP login
				<?php }?>
				<p>Enabling LDAP login will protect your login page by your configured LDAP. <b>Please check this only after you have successfully tested your configuration</b> as the default WordPress login will stop working.</p>
			</form>
			<script>
				jQuery('#enable_ldap_login').change(function() {
					jQuery('#enable_login_form').submit();
				});
			</script>
			<form name="f" id="enable_admin_wp_login" method="post" action="">
				<input type="hidden" name="option" value="mo_ldap_local_enable_admin_wp_login" />
				<input type="checkbox" id="mo_ldap_local_enable_admin_wp_login" name="mo_ldap_local_enable_admin_wp_login" value="1" <?php checked(get_option('mo_ldap_local_enable_admin_wp_login') == 1);?> /> Authenticate Administrators from both LDAP and WordPress<br><br>
				<input type="checkbox" id="" name="" disabled /> Authenticate WP Users from both LDAP and WordPress <b> ( Supported in <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version </a> of the plugin. )</b>
			</form>
			<!--<br>
			<form name="f" id="enable_fallback_login_form" method="post" action="">
				<input type="hidden" name="option" value="mo_ldap_local_fallback_login" />
				<input type="checkbox" id="mo_ldap_local_enable_fallback_login" name="mo_ldap_local_enable_fallback_login" value="1" <?php checked(get_option('mo_ldap_local_enable_fallback_login') == 1);?> />Enable Fallback Login with Wordpress password(If LDAP Server is unreacheable). This will update your local Wordpress password as your LDAP password. RECOMMENDED to be <b>FALSE</b>
			</form>-->
			<br>
			<!-- Toggle checkbox -->
			<form name="f" id="enable_register_user_form" method="post" action="">
				<input type="hidden" name="option" value="mo_ldap_local_register_user" />
				<input type="checkbox" id="mo_ldap_local_register_user" name="mo_ldap_local_register_user" value="1" <?php checked(get_option('mo_ldap_local_register_user') == 1);?> />Enable Auto Registering users if they do not exist in WordPress
			</form>
			<script>
				jQuery('#mo_ldap_local_register_user').change(function() {
					jQuery('#enable_register_user_form').submit();
				});
				jQuery('#enable_fallback_login_form').change(function() {
					jQuery('#enable_fallback_login_form').submit();
				});
				jQuery('#enable_admin_wp_login').change(function() {
					jQuery('#enable_admin_wp_login').submit();
				});
			</script>
			<br/>
		</div>

		<div class="mo_ldap_small_layout">
			<script>
				function ping_server(){
						var ldapServerUrl = document.getElementById('ldap_server').value;
						if(!ldapServerUrl || ldapServerUrl.trim() == ""){
							alert("Enter LDAP Server URL");
						} else{
							var option = document.getElementById("mo_ldap_local_configuration_form_action").value = "mo_ldap_local_ping_server";
							//alert(document.getElementById("mo_ldap_configuration_form_action").value);
							var configForm = document.getElementById("mo_form1");
							//alert(configForm);
							configForm.submit();
						}
				}
			</script>
			<!-- Save LDAP Configuration -->
			<form id="mo_form1" name="f" method="post" action="">
				<input id="mo_ldap_local_configuration_form_action" type="hidden" name="option" value="mo_ldap_local_save_config" />
				<!-- Copy default values to configuration -->
				<p><strong style="font-size:14px;">NOTE: </strong> You need to find out the values for the below given fields from your LDAP Administrator.</strong></p>
				<h3 class="mo_ldap_left">LDAP Connection Information</h3>

				<div id="panel1">
					<table class="mo_ldap_settings_table">
						<tr>
							<td style="width: 24%"><b><font color="#FF0000">*</font>LDAP Server:</b></td>
							<td><input class="mo_ldap_table_textbox <?php echo $mo_ldap_local_server_url_status; ?>" type="url" id="ldap_server" name="ldap_server" required placeholder="ldap://<server_address or IP>:<port>" value="<?php echo $server_url?>" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><i>Specify the host name for the LDAP server eg: ldap://myldapserver.domain:389 , ldap://89.38.192.1:389. When using SSL, the host may have to take the form ldaps://host:636.</i></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="button" class="button button-primary button-large" onclick="ping_server();" value="Contact LDAP Server" />&nbsp;&nbsp;<span id="pingResult"></span></td>
							<td></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input type="checkbox" name="mo_ldap_local_tls_enable" value="1" <?php checked(get_option('mo_ldap_local_use_tls') == 1);?> onchange="enabletls(this)"> <b>Enable TLS</b> (Check this only if your server use TLS Connection.)
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Service Account DN:</b></td>
							<td><input class="mo_ldap_table_textbox <?php echo $mo_ldap_local_service_account_status; ?>" type="text" id="dn" name="dn" required placeholder="CN=service,DC=domain,DC=com" value="<?php echo $dn?>" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><i>This service account will be used to establish the connection.<br>Specify the Service Account DN(distinguished Name) of the LDAP server. e.g. cn=username,cn=group,dc=domain,dc=com<br/>uid=username,ou=organisational unit,dc=domain,dc=com.</i></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Service Account Password:</b></td>
							<td><input class="mo_ldap_table_textbox <?php echo $mo_ldap_local_service_account_status; ?>" required type="password" name="admin_password" placeholder="Enter password of Service Account" value="<?php echo $admin_password?>"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><i>Password for the Service Account in the LDAP Server.</i></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary button-large" value="Test Connection & Save"/>&nbsp;&nbsp; <input
								type="button" id="conn_help" class="help" value="Troubleshooting" /></td>
						</tr>
						<tr>
							<td colspan="2" id="conn_troubleshoot" hidden>
								<p>
									<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
									<ol>
										<li>Please make sure that all the values entered are correct.</li>
										<li>If you are having firewall, open the firewall to allow incoming requests to your LDAP from your WordPress <b>Server IP</b> and <b>port 389.</b></li>
										<li>If you are still having problems, submit a query using the support panel on the right hand side.</li>
									</ol>
								</p>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>

		<form name="f" id="mo_ldap_local_tls_enable_form" method="post" action="" style="display:none">
			<input type="hidden" name="option" value="mo_ldap_local_tls_enable">
			<input type="checkbox" id="mo_ldap_local_tls_enable" name="mo_ldap_local_tls_enable" value="1"> <b>Enable TLS</b> (Check this only if your server use TLS Connection.)
		</form>
		<script>
			function enabletls(enabletls){
				if(enabletls.checked)
					jQuery("#mo_ldap_local_tls_enable").prop('checked', true);
				else
					jQuery("#mo_ldap_local_tls_enable").prop('checked', false);
				jQuery("#mo_ldap_local_tls_enable_form").submit();
			}
		</script>

		<div class="mo_ldap_small_layout">
		<h3>LDAP User Mapping Configuration</h3>
			<form id="mo_form1" name="f" method="post" action="">
				<input id="mo_ldap_local_configuration_form_action" type="hidden" name="option" value="mo_ldap_local_save_user_mapping" />
				<div id="panel1">
					<table class="mo_ldap_settings_table">
						<tr>
							<td style="width: 24%"></td>
							<td></td>
						</tr>

						<tr>
							<td><b><font color="#FF0000">*</font>Search Base(s):</b></td>
							<td><input class="mo_ldap_table_textbox  <?php echo $mo_ldap_local_user_mapping_status; ?>" type="text" id="search_base" name="search_base" required placeholder="dc=domain,dc=com" value="<?php echo $search_base?>" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><i>This is the LDAP Tree under which we will search for the users for authentication.  If we are not able to find a user in LDAP it means they are not present in this search base or any of its sub trees. They may be present in some other search base.<br> Provide the distinguished name of the Search Base object. <b>eg. cn=Users,dc=domain,dc=com</b>.

							<?php if(get_option('mo_ldap_local_cust', '1') == '0'){ ?>
								<br><b>Multiple Search Bases are supported in the <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version</a> of the plugin.</b></i><br><br></td>
							<?php }else{ ?>
								If you have users in different locations in the directory(OU's), separate the distinguished names of the search base objects by a semi-colon(;). <b>eg. cn=Users,dc=domain,dc=com; ou=people,dc=domian,dc=com</b></i></td>
							<?php } ?>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>LDAP Search Filter:</b></td>
							<td><input class="mo_ldap_table_textbox <?php echo $mo_ldap_local_user_mapping_status; ?>" type="text" id="search_filter" name="search_filter" required placeholder="(&(objectClass=*)(cn=?))" value="<?php echo $search_filter?>"
							pattern=".*\?.*" title="Must contain Question Mark(?) for attributes you want to match e.g. (&(objectClass=*)(uid=?))" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><i>This field is important for two reasons. <br>1. While searching for users, this is the attribute that is going to be matched to see if the user exists.  <br>2. If you want your users to login with their username or firstname.lastname or email - you need to specify those options in this field. e.g. <b>(&(objectClass=*)(&lt;LDAP_ATTRIBUTE&gt;=?))</b>. Replace <b>&lt;LDAP_ATTRIBUTE&gt;</b> with the attribute where your username is stored. Some common attributes are
							<ol>
							<table>
								<tr><td style="width:50%">common name</td><td>(&(objectClass=*)(<b>cn</b>=?))</td></tr>
								<tr><td>email</td><td>(&(objectClass=*)(<b>mail</b>=?))</td></tr>
								<tr><td>logon name</td><td>(&(objectClass=*)(<b>sAMAccountName</b>=?))<br/>(&(objectClass=*)(<b>userPrincipalName</b>=?))</td></tr>
								<tr><td>custom attribute where you store your WordPress usernames use</td> <td>(&(objectClass=*)(<b>customAttribute</b>=?))</td></tr>

							</table><br>
							You can even allow logging in with multiple attributes e.g. you can allow logging in with username or email e.g. (&(objectClass=*)(<b>|</b>(<b>cn=?</b>)(<b>mail=?</b>)))
							</ol>
						</tr>
						<tr><td></td><td>Please make clear that the attributes that we are showing are examples and the actual ones could be different. These should be confirmed with the LDAP Admin.</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary button-large" value="Save User Mapping"/>&nbsp;&nbsp; <input
								type="button" id="conn_help_user_mapping" class="help" value="Troubleshooting" /></td>
						</tr>
						<tr>
							<td colspan="2" id="conn_user_mapping_troubleshoot" hidden>
									<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
									<ol>
										<li>The <b>search base</b> URL is typed incorrectly. Please verify if that search base is present.</li>
										<li>User is not present in that search base. The user may be present in the directory but in some other tree and you may have entered a tree where this users is not present.</li>
										<li><b>Search filter</b> is incorrect - User is present in the search base but the username is mapped to a different attribute in the search filter. E.g. you may be logging in with username and may have mapped it to the email attribute. So this wont work. Please make sure that the right attribute is mentioned in the search filter (with which you want the mapping to happen)</li>
										<li>Please make sure that the user is present and test with the right user.</li>
										<li>If you are still having problems, submit a query using the support panel on the right hand side.</li>
									</ol>

							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>

		<div class="mo_ldap_small_layout">
		<!-- Authenticate with LDAP configuration -->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_ldap_local_test_auth" />
			<h3>Test Authentication</h3>

				<?php if(get_option('mo_ldap_local_cust', '1') == '0'){ ?>
					Wordpress username is mapped to the <b>LDAP attribute defined in the Search Filter</b> attribute in LDAP. Ensure that you have an administrator user in LDAP with the same attribute value. <br><br>
				<?php } ?>
			<div id="test_conn_msg"></div>
			<div id="panel1">
				<table class="mo_ldap_settings_table">
					<tr>
						<td style="width: 24%"><b><font color="#FF0000">*</font>Username:</b></td>
						<td><input class="mo_ldap_table_textbox  <?php if(isset($_POST['test_username']))echo $mo_ldap_local_user_mapping_status; ?>" type="text" name="test_username" required placeholder="Enter username" value="<?php if(isset($_POST['test_username']))echo $_POST['test_username']; ?>" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_ldap_table_textbox <?php if($mo_ldap_local_user_mapping_status=="mo_ldap_input_success")echo $mo_ldap_local_username_status; ?>" type="password" name="test_password" required placeholder="Enter password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" class="button button-primary button-large" value="Test Authentication"/>&nbsp;&nbsp; <input
								type="button" id="auth_help" class="help" value="Troubleshooting" /></td>
					</tr>
					<tr>
						<td colspan="2" id="auth_troubleshoot" hidden>
							<p>
								<strong>User is not getting authenticated? Check the following:</strong>
								<ol>
									<li>The username-password you are entering is correct.</li>
									<li>The user is not present in the search bases you have specified against <b>SearchBase(s)</b> above.</li>
									<li>Your Search Filter may be incorrect and the username mapping may be to an LDAP attribute other than the ones provided in the Search Filter</li>
								</ol>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</form>
		</div>
		<script>
			<?php if (!Mo_Ldap_Local_Util::is_customer_registered()) { ?>
				jQuery( document ).ready(function() {
						jQuery("#configurationForm :input").prop("disabled", true);
						jQuery("#configurationForm :input[type=text]").val("");
						jQuery("#configurationForm :input[type=url]").val("");
					});
			<?php } ?>
		</script>
	<?php
}
/* End of Configure LDAP function */

function mo_ldap_local_troubleshooting(){
	?>

	<div class="mo_ldap_table_layout">
		<table class="mo_ldap_help">
					<tbody><tr>
						<td class="mo_ldap_help_cell">
							<div id="help_curl_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How to enable PHP cURL extension? (Pre-requisite)</div>
							</div>
							<div hidden="" id="help_curl_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Open php.ini file located under php installation folder.</li>
									<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_curl.dll</b>. </li>
									<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
									<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_ldap_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How to enable PHP LDAP extension? (Pre-requisite)</div>
							</div>
							<div hidden="" id="help_ldap_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Ensure that <b>php_ldap.dll</b> exists in you PHP extension directory.</li>
									<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_ldap.dll</b>. </li>
									<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
									<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Add the files <b>libeay32.dll</b> and <b>ssleay32.dll</b> in the root PHP installation directory if not exist.</li>
									<li>Step 5:&nbsp;&nbsp;&nbsp;&nbsp;Copy <b>libsasl.dll</b> from the root PHP installation director to the Apache bin directory if it does not already exist.
									</li>
									<li>Step 6:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
						<div id="help_ping_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Contact LDAP Server not working?</div>
							</div>
							<div hidden="" id="help_ping_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Check your LDAP Server URL to see if it is correct.<br>
									 eg. ldap://myldapserver.domain:389 , ldap://89.38.192.1:389. When using SSL, the host may have to take the form ldaps://host:636.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;Your LDAP Server may be behind a firewall. Check if the firewall is open to allow requests from your Wordpress installation.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_invaliddn_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Test LDAP Configuration not working?</div>
							</div>
							<div hidden="" id="help_invaliddn_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Check if you have entered valid Service Account DN(distinguished Name) of the LDAP server. <br>e.g. cn=username,cn=group,dc=domain,dc=com<br>
									uid=username,ou=organisational unit,dc=domain,dc=com</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;Check if you have entered correct Password for the Service Account.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_invalidsf_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Why is Test Authentication not working?</div>
							</div>
							<div hidden="" id="help_invalidsf_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;The username/password combination you provided may be incorrect.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;You may have provided a <b>Search Base(s)</b> in which the user does not exist.</li>
									<li>3.&nbsp;&nbsp;&nbsp;&nbsp;Your username is present in an LDAP attribute other than <b>CN</b>. Support for other LDAP attributes as username is present in the <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version </a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_seracccre_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What are the LDAP Service Account Credentials?</div>
							</div>
							<div hidden="" id="help_seracccre_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Service account is an non privileged user which is used to bind to the LDAP Server. It is the preferred method of binding to the LDAP Server if you have to perform search operations on the directory.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;The distinguished name(DN) of the service account object and the password are provided as credentials.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_sbase_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is meant by Search Base in my LDAP environment?</div>
							</div>
							<div hidden="" id="help_sbase_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Search Base denotes the location in the directory where the search for a particular directory object begins.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;It is denoted as the distinguished name of the search base directory object. eg: CN=Users,DC=domain,DC=com.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_sfilter_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">What is meant by Search Filter in my LDAP environment? <font color="#FF0000">*PREMIUM*</font></div>
							</div>
							<div hidden="" id="help_sfilter_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Search Filter is a basic LDAP Query for searching users based on mapping of username to a particular LDAP attribute.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;The following are some commonly used Search Filters. You will need to use a search filter which uses the attributes specific to your LDAP environment. Confirm from your LDAP administrator.</li>
										<ul>
											<table>
												<tr><td style="width:50%">common name</td><td>(&(objectClass=*)(<b>cn</b>=?))</td></tr>
												<tr><td>email</td><td>(&(objectClass=*)(<b>mail</b>=?))</td></tr>
												<tr><td>logon name</td><td>(&(objectClass=*)(<b>sAMAccountName</b>=?))<br/>(&(objectClass=*)(<b>userPrincipalName</b>=?))</td></tr>
												<tr><td>custom attribute where you store your WordPress usernames use</td> <td>(&(objectClass=*)(<b>customAttribute</b>=?))</td></tr>
												<tr><td>if you store Wordpress usernames in multiple attributes(eg: some users login using email and others using their username)</td><td>(&(objectClass=*)(<b>|</b>(<b>cn=?</b>)(<b>mail=?</b>)))</td></tr>
											</table>
										</ul>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_ou_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How do users present in different Organizational Units(OU's) login into Wordpress? <font color="#FF0000">*PREMIUM*</font></div>
							</div>
							<div hidden="" id="help_ou_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Support for multiple search bases is present in the <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version</a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_loginusing_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">Some of my users login using their email and the rest using their usernames. How will both of them be able to login?<font color="#FF0000"> *PREMIUM*</font></div>
							</div>
							<div hidden="" id="help_loginusing_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>Support for multiple username attributes is present in the <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version</a> of the plugin.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_rolemap_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How Role Mapping works?</div>
							</div>
							<div hidden="" id="help_rolemap_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>1.&nbsp;&nbsp;&nbsp;&nbsp;Assign groups to Users in your LDAP.</li>
									<li>2.&nbsp;&nbsp;&nbsp;&nbsp;Configure User Role Mapping against LDAP Groups. If user belongs to multiple groups, mapping which have <b>Highest WordPress Role</b> will have higher priority.</li>
									<li>3.&nbsp;&nbsp;&nbsp;&nbsp;For each user login mapping will be checked and user role will be updated if different.</li>
									<li>4.&nbsp;&nbsp;&nbsp;&nbsp;If user does not belong to any group, user role will be updated with default mapping value.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

					<tr>
						<td class="mo_ldap_help_cell">
							<div id="help_multiplegroup_title" class="mo_ldap_title_panel">
								<div class="mo_ldap_help_title">How Role Mapping works if user belongs to multiple groups?</div>
							</div>
							<div hidden="" id="help_multiplegroup_desc" class="mo_ldap_help_desc" style="display: none;">
								<ul>
									<li>If user belongs to multiple groups, <b>Highest Role</b> will be assigned to the User from all matched Roles.</li>
								</ul>
								For any further queries, please contact us.
							</div>
						</td>
					</tr>

				</tbody></table>
	</div>


	<?php

}


/* Test Default Configuration*/
function mo_ldap_local_rolemapping() {
?>
<div class="mo_ldap_small_layout" style="margin-top:0px;">

	<?php if(!Mo_Ldap_Local_Util::is_customer_registered()) { ?>
				<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'account'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange SAML Plugin.</div>
			<?php } ?>

		<form name="f" id="enable_role_mapping_form" method="post" action="">
				<input type="hidden" name="option" value="mo_ldap_local_enable_role_mapping" />
				<h3>LDAP Groups to WP User Role Mapping</h3>
				<input type="checkbox" id="enable_ldap_role_mapping" name="enable_ldap_role_mapping"  value="1" <?php checked(get_option('mo_ldap_local_enable_role_mapping') == 1);?> />Enable Role Mapping
				<p>Enabling Role Mapping will automatically map Users from LDAP Groups to below selected WordPress Role. Role mapping will not be applicable for primary admin of wordpress.</p>
			</form>


	<form id="role_mapping_form" name="f" method="post" action="">
				<input id="mo_ldap_local_user_mapping_form" type="hidden" name="option" value="mo_ldap_local_save_mapping" />

				<div id="panel1">
					<table class="mo_ldap_mapping_table" id="ldap_role_mapping_table" style="width:90%">
							<tr>
								<td colspan=2><i> Default role will be assigned to all users for which mapping is not specified.</i></td>
							</tr>
							<tr>
							<td><font style="font-size:13px;font-weight:bold;">Default Role </font><!-- input class="mo_ldap_table_textbox" type="text" readonly name="mapping_key_default"
								required value="Default Role" /-->
							</td>
							<td>
								<select name="mapping_value_default" style="width:100%" id="default_group_mapping" >
								   <?php
								   	 if(get_option('mo_ldap_local_mapping_value_default'))
								   	 	$default_role = get_option('mo_ldap_local_mapping_value_default');
								   	 else
								   	 	$default_role = get_option('default_role');
								   	 wp_dropdown_roles($default_role); ?>
								</select>
								<select style="display:none" id="wp_roles_list">
								   <?php wp_dropdown_roles($default_role); ?>
								</select>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td style="width:50%"><b>LDAP Group Name</b></td>
							<td style="width:50%"><b>WordPress Role</b></td>
						</tr>

						<?php
						$mapping_count = 1;
						if(is_numeric(get_option('mo_ldap_local_role_mapping_count')))
							$mapping_count = intval(get_option('mo_ldap_local_role_mapping_count'));
						for($i = 1 ; $i <= $mapping_count ; $i++){

						?>
						<tr>
							<td><input class="mo_ldap_table_textbox" type="text" name="mapping_key_<?php echo $i;?>"
								 value="<?php echo get_option('mo_ldap_local_mapping_key_'.$i);?>"  placeholder="cn=group,dc=domain,dc=com"  />
							</td>
							<td>
								<select name="mapping_value_<?php echo $i;?>" id="role" style="width:100%"  >
								   <?php wp_dropdown_roles( get_option('mo_ldap_local_mapping_value_'.$i) ); ?>
								</select>
							</td>
						</tr>
						<?php }

						if($mapping_count==0){ ?>
						<tr>
							<td><input class="mo_ldap_table_textbox" type="text" name="mapping_key_1"
								 value="" placeholder="cn=group,dc=domain,dc=com" />
							</td>
							<td>
								<select name="mapping_value_1" id="role" style="width:100%"  >
								   <?php wp_dropdown_roles(); ?>
								</select>
							</td>
						</tr>
						<?php }

						?>
						</table>
						<table class="mo_ldap_mapping_table" style="width:90%;">

						<tr><td><a style="cursor:pointer" id="add_mapping">Add More Mapping</a><br><br></td><td>&nbsp;</td></tr>


						<tr>
								<td colspan=2><i> Specify attribute which stores group names to which LDAP Users belong.</i></td>
							</tr>
							<tr>
							<td style="width:50%"><font style="font-size:13px;font-weight:bold;">LDAP Group Attributes Name </font>
							</td>
							<td>
								  <?php
								   	 if(!get_option('mo_ldap_local_mapping_memberof_attribute'))
								   	 	update_option( 'mo_ldap_local_mapping_memberof_attribute', 'memberOf' );
								   	 $mapping_memberof_attribute = get_option('mo_ldap_local_mapping_memberof_attribute');
								   ?>
								<input type="text" name="mapping_memberof_attribute" required="true" placeholder="Group Attributes Name" style="width:100%;" value="<?php echo $mapping_memberof_attribute;?>"  >
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>

						<tr>
							<td><input type="submit" class="button button-primary button-large" value="Save Mapping" /></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</form>

			<form method="post" id="rolemappingtest">
			<br>
				<h3>Test Role Mapping Configuration</h3>Enter LDAP username to test role mapping configuration
				<table id="attributes_table" class="mo_ldap_settings_table">
					<tbody><tr></tr>
					<tr></tr>
					<tr>
						<td>Username</td>
						<td><input type="text" id="mo_ldap_username" name="mo_ldap_username" required="" placeholder="Enter Username" style="width:61%;"  >
					</td></tr>
					<tr>
					<!--<td><input type="submit" value="Test Configuration" class="button button-primary button-large" /></td>-->
					<td><input type="button" value="Test Configuration" class="button button-primary button-large" onclick="testRoleMappingConfiguration()"  ></td>
					</tr>
				</tbody></table>
			</form><br><br>
</div>
<script>
	jQuery( document ).ready(function() {
		jQuery("#default_group_mapping option[value='administrator']").remove();
	});
	jQuery('#enable_ldap_role_mapping').change(function() {
		jQuery('#enable_role_mapping_form').submit();
	});
	jQuery('#add_mapping').click(function() {
		var last_index_name = jQuery('#ldap_role_mapping_table tr:last .mo_ldap_table_textbox').attr('name');
		var splittedArray = last_index_name.split("_");
		var last_index = parseInt(splittedArray[splittedArray.length-1])+1;
		var dropdown = jQuery("#wp_roles_list").html();
		var new_row = '<tr><td><input class="mo_ldap_table_textbox" type="text" placeholder="cn=group,dc=domain,dc=com" name="mapping_key_'+last_index+'" value="" /></td><td><select name="mapping_value_'+last_index+'" style="width:100%" id="role">'+dropdown+'</select></td></tr>';
		jQuery('#ldap_role_mapping_table tr:last').after(new_row);
	});
	jQuery("#rolemappingtest").submit(function(event ) {
		event.preventDefault();
		testRoleMappingConfiguration();
	});
	function testRoleMappingConfiguration(){
		var username = jQuery("#mo_ldap_username").val();
		var myWindow = window.open('<?php echo site_url(); ?>' + '/?option=testrolemappingconfig&user='+username, "Test Attribute Configuration", "width=600, height=600");
	}
</script>
<script>
	<?php if (!Mo_Ldap_Local_Util::is_customer_registered()) { ?>
		jQuery( document ).ready(function() {
				jQuery("#enable_role_mapping_form :input").prop("disabled", true);
				jQuery("#enable_role_mapping_form :input[type=text]").val("");
				jQuery("#enable_role_mapping_form :input[type=url]").val("");
				jQuery("#role_mapping_form :input").prop("disabled", true);
				jQuery("#role_mapping_form :input[type=text]").val("");
				jQuery("#role_mapping_form :input[type=url]").val("");
			});
	<?php } ?>
</script>
<?php
}

/* Show OTP verification page*/
function mo_ldap_local_show_otp_verification(){
	?>
		<div class="mo_ldap_table_layout">
			<div id="panel2">
				<table class="mo_ldap_settings_table">
		<!-- Enter otp -->
					<form name="f" method="post" id="ldap_form" action="">
						<input type="hidden" name="option" value="mo_ldap_local_validate_otp" />
						<h3>Verify Your Email</h3>
						<tr>
							<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
							<td colspan="2"><input class="mo_ldap_table_textbox" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP" style="width:61%;" />
							 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('resend_otp_form').submit();">Resend OTP over Email</a></td>
						</tr>
						<tr><td colspan="3"></td></tr>
						<tr><td></td><td>
						<input type="button" value="Back" id="back_btn" class="button button-primary button-large" />
						<input type="submit" value="Validate OTP" class="button button-primary button-large" />
						</td>
						</form>
						<td><form method="post" action="" id="mo_ldap_cancel_form">
							<input type="hidden" name="option" value="mo_ldap_local_cancel" />
						</form></td></tr>
					<form name="f" id="resend_otp_form" method="post" action="">
							<td>
							<input type="hidden" name="option" value="mo_ldap_local_resend_otp"/>
							</td>
						</tr>
					</form>
				</table>
				<br>
				<hr>

				<h3>I did not recieve any email with OTP . What should I do ?</h3>
				<form id="phone_verification" method="post" action="">
					<input type="hidden" name="option" value="mo_ldap_local_phone_verification" />
					 If you can't see the email from miniOrange in your mails, please check your <b>SPAM Folder</b>. If you don't see an email even in SPAM folder, verify your identity with our alternate method.
					 <br><br>
						<b>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</b><br><br><input class="mo_ldap_table_textbox" required="true" pattern="[\+]\d{1,3}\d{10}" autofocus="true" type="text" name="phone_number" id="phone" placeholder="Enter Phone Number" style="width:40%;" value="<?php echo get_option('mo_ldap_local_admin_phone');  ?>" title="Enter phone number without any space or dashes."/>
						<br><input type="submit" value="Send OTP" class="button button-primary button-large" />

				</form>
			</div>
		</div>
		<script>
	jQuery("#phone").intlTelInput();
	jQuery('#back_btn').click(function(){
			jQuery('#mo_ldap_cancel_form').submit();
	});

</script>
<?php
}
/* End Show OTP verification page*/



function mo_ldap_show_attribute_mapping_page(){
	?>
		<div class="mo_ldap_table_layout">
			<div id="panel2">
			<form name="f" method="post" id="attribute_config_form">
				<table id="attributes_table" class="mo_ldap_settings_table">

						<input type="hidden" name="option" value="mo_ldap_save_attribute_config" />
						<h3>Attribute Configuration</h3>
						<tr>
							<td style="width:70%;">Enter the LDAP attribute names for Email, Phone, First Name and Last Name attributes</td>
						</tr>
						<tr>
							<td><input type="checkbox" id="enable_update_ldap" name="enable_update_ldap"  value="1" <?php checked(get_option('mo_ldap_local_enable_update_ldap') == 1);?>  />Enable updating information in LDAP when user edits profile</td>
						</tr>
						<tr>
							<td style="width:40%;"><b><font color="#FF0000">*</font>Email Attribute</b></td>
							<td><input type="text" name="mo_ldap_email_attribute" required placeholder="Enter Email attribute" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_email_attribute');?>"/></td>
						</tr>
						<tr>
							<td style="width:40%;"><b><font color="#FF0000">*</font>Phone Attribute</b></td>
							<td><input type="text" name="mo_ldap_phone_attribute" required placeholder="Enter Phone attribute" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_phone_attribute');?>"/></td>
						</tr>
						<tr>
							<td style="width:40%;"><b><font color="#FF0000">*</font>First Name Attribute</b></td>
							<td><input type="text" name="mo_ldap_fname_attribute" required placeholder="Enter First Name attribute" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_fname_attribute');?>" /></td>
						</tr>
						<tr>
							<td style="width:40%;"><b><font color="#FF0000">*</font>Last Name Attribute</b></td>
							<td><input type="text" name="mo_ldap_lname_attribute" required placeholder="Enter Last Name attribute" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_lname_attribute');?>" /></td>
						</tr>

						<?php
							if(get_option('mo_ldap_local_cust', '1') != '0'){
								$custom_attributes = array();
								$wp_options = wp_load_alloptions();
								foreach($wp_options as $option=>$value){
									if(strpos($option, "mo_ldap_local_custom_attribute") === false){
										//Do nothing
									} else{
										?><tr>
											<td><b><font color="#FF0000"></font><?php echo $value; ?> Attribute</b></td>
											<td><b><?php echo get_option($option); ?></b></td>
											<td><a style="cursor:pointer;" onclick="deleteAttribute('<?php echo $value; ?>');">Delete</a></td>
										</tr><?php
									}
								}
							?>
							<tr><td><h3>Add Custom Attributes</h3></td></tr>
							<tr>
								<td>Enter extra LDAP attributes which you wish to be included in the user profile</td>
							</tr>
							<tr>
								<td><input type="text" name="mo_ldap_local_custom_attribute_1_name" placeholder="Custom Attribute Name" style="width:61%;" /></td>
								<td><input type="button" name="add_attribute" value="+" onclick="add_custom_attribute();" class="button button-primary" />&nbsp;
								<input type="button" name="remove_attribute" value="-" onclick="remove_custom_attribute();" class="button button-primary" /></td>
							</tr>

						<?php

							} else{
								?><tr><td><b>Support for custom profile attributes from LDAP is present in the <a href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Premium version </a> of the plugin.</b></i></td></tr>

							<?php }

						?>

						<tr id="save_config_element">
							<td>
								<input type="submit" value="Save Configuration" class="button button-primary button-large" />
								<a id="back_button" href=""class="button button-primary button-large">Cancel</a>
							</td>
						</tr>

				</table>
				</form>
			</div><br>
			<?php if(get_option('mo_ldap_local_cust', '1') != '0'){ ?>
				<form id="delete_custom_attribute_form" method="post">
					<input type="hidden" name="option" value="mo_ldap_delete_custom_attribute" />
					<input type="hidden" id="custom_attribute_name" name="custom_attribute_name" value="" />
				</form>
			<?php } ?>
			<form method="post" id="attribiteconfigtest">
				<input type="hidden" name="option" value="mo_ldap_test_attribute_configuration" />
				<table id="attributes_table" class="mo_ldap_settings_table">
					<tr><h3>Test Attribute Configuration</h3></tr>
					<tr>Enter LDAP username to test attribute configuration</tr>
					<tr>
						<td>Username</td>
						<td><input type="text" id="mo_ldap_username" name="mo_ldap_username" required placeholder="Enter Username" style="width:61%;" />
					</tr>
					<tr>
					<!--<td><input type="submit" value="Test Configuration" class="button button-primary button-large" /></td>-->
					<td><input type="button" value="Test Configuration" class="button button-primary button-large" onclick="testConfiguration()" /></td>
					</tr>
				</table>
			</form>
			<script>

			var countAttributes;
			function add_custom_attribute(){
				countAttributes += 1;
				jQuery("<tr id='row_" + countAttributes + "'><td><input type='text' id='mo_ldap_local_custom_attribute_" + countAttributes + "_name' name='mo_ldap_local_custom_attribute_" + countAttributes + "_name' placeholder='Custom Attribute Name' style='width:61%;' /></td></tr>").insertBefore(jQuery("#save_config_element"));

			}

			function remove_custom_attribute(){
				jQuery("#row_" + countAttributes).remove();
				countAttributes -= 1;
				if(countAttributes == 0)
					countAttributes = 1;
			}

			jQuery("#attribiteconfigtest").submit(function(event ) {
				event.preventDefault();
				testConfiguration();
			});

			jQuery("#attribiteconfigtest").submit(function(event ) {
				event.preventDefault();
				testConfiguration();
			});

			function testConfiguration(){
				var username = jQuery("#mo_ldap_username").val();
				var myWindow = window.open('<?php echo site_url(); ?>' + '/?option=testattrconfig&user='+username, "Test Attribute Configuration", "width=600, height=600");
			}

			function deleteAttribute(attributeName){
				jQuery("#custom_attribute_name").val(attributeName);
				jQuery("#delete_custom_attribute_form").submit();
			}

			jQuery(document).ready(function(){
				countAttributes = 1;
			});

			</script>
		</div>
<?php
}

function mo_ldap_show_user_management_page(){
	?>
		<div class="mo_ldap_table_layout">
			<div id="panel2">
				<table class="mo_ldap_settings_table">
					<form name="f" method="post" id="attribute_config_form" action="">
						<input type="hidden" name="option" value="mo_ldap_user_management_config" />
						<h3>User Management Configuration</h3>
						<tr>
							<td><b><font color="#FF0000">*</font>DN of New User Location in Directory</b></td>
							<td><input type="text" name="mo_ldap_new_user_location" required placeholder="DN of New Users in LDAP" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_new_user_location');?>"/></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Object Class of User</b></td>
							<td><input type="text" name="mo_ldap_objectclass_attribute" required placeholder="Object Class of Users in LDAP" style="width:80%;"
							value="<?php echo get_option('mo_ldap_local_objectclass_attribute');?>"/></td>
						</tr>
						<tr><td colspan="3"></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							<input type="submit" value="Save Configuration" class="button button-primary button-large" />
							<a id="back_button" href=""class="button button-primary button-large">Cancel</a>
							</td>

					</form>
				</table>
			</div>
		</div>
<?php
}

function show_pricing_page() { ?>
		<div class="mo_ldap_table_layout">
		<table class="mo_ldap_local_pricing_table">
		<h2>Licensing Plans
		<span style="float:right"><input type="button" name="ok_btn" id="ok_btn" class="button button-primary button-large" value="OK, Got It" onclick="window.location.href='admin.php?page=mo_ldap_local_login&mo2f_tab=default'" /></span>
		</h2><hr>
		<tr style="vertical-align:top;">

				<td>
				<div class="mo_ldap_local_thumbnail mo_ldap_local_pricing_free_tab" style="width:233px;">

				<h3 class="mo_ldap_local_pricing_header">Free</h3>
				<h4 class="mo_ldap_local_pricing_sub_header" style="margin-bottom:2px;padding-bottom:11px;">(You are automatically on this plan)</h4>

				<hr>
				<p class="mo_ldap_local_pricing_text" style="padding:10px;">$0</p>
				<p></p>
				<hr>

				<p class="mo_ldap_local_pricing_text" style="padding-bottom:23px;"> Unlimited LDAP Authentications<br>
				Role Mapping<br>
				Basic Attribute Mapping<br>
			    <br><br><br><br><br><br><br><br><br><br><br><br<br><br><br><br><br></p>
				<hr>
				<p class="mo_ldap_local_pricing_text" style="padding-top:0px;padding-bottom:0px !important;">Basic Support by Email</p>
				</td>
				<td>
				<div class="mo_ldap_local_thumbnail mo_ldap_local_pricing_paid_tab" >

				<h3 class="mo_ldap_local_pricing_header">Do it yourself</h3>
				<p></p>
				<h4 class="mo_ldap_local_pricing_sub_header" style="padding-bottom:8px !important;"><a class="button button-primary button-large"
				 onclick="upgradeform('wp_ldap_intranet_basic_plan')" >Click here to upgrade</a> *</h4>
				<p></p>
				<hr>
				<p class="mo_ldap_local_pricing_text" style="padding:10px;">$119 - One Time Payment</p>

				<hr>
				<p class="mo_ldap_local_pricing_text" style="padding-bottom:16px !important;">
					Unlimited LDAP Authentications<br>
					Role Mapping<br>
					Customized Attribute Mapping<br>
					Multiple Search Bases<br>
					Multi-Site Support<br>
					Integrated Windows Authentication supported*<br>
					Multiple LDAP Support**<br>
					Sync LDAP profile with WordPress *****<br>
					Brute Force Protection *******<br>
					Failed Login Notification ********<br><br><br><br><br><br>
				</p>
				<br>
				<hr>

				<p class="mo_ldap_local_pricing_text" >Basic Support by Email</p>
				</div></td>
				<td>
				<div class="mo_ldap_local_thumbnail mo_ldap_local_pricing_free_tab" >
				<h3 class="mo_ldap_local_pricing_header">Premium</h3>
				<p></p>
				<h4 class="mo_ldap_local_pricing_sub_header" style="padding-bottom:8px !important;"><a class="button button-primary button-large"
				 onclick="upgradeform('wp_ldap_intranet_premium_plan')" >Click here to upgrade</a> *</h4>
				<p></p>
				<hr>
				<p class="mo_ldap_local_pricing_text">$119 + One Time Setup Fees <br>
				( $60 per hour )</p>
				<hr>

				<p class="mo_ldap_local_pricing_text">
					Unlimited LDAP Authentications<br>
					Role Mapping<br>
					Customized Attribute Mapping<br>
					Multiple Search Bases<br>
					Multi-Site Support<br>
					Integrated Windows Authentication supported*<br>
					Customization<br>
					Multiple LDAP Support**<br>
					End to End Ldap Integration ***<br>
					Integration with other plugins ****<br>
					Sync LDAP profile with WordPress *****<br>
					Outbound LDAP password sync ******<br>
					Brute Force Protection *******<br>
					Failed Login Notification ********<br>
					Profile Photos Sync *********<br>
					NTLM SSO **********<br>
				</p>

				<hr>

				<p class="mo_ldap_local_pricing_text">Premium Support Plans Available</p>

				</div></td>

		</tr>
		</table>
		<form style="display:none;" id="loginform" action="<?php echo get_option( 'mo_ldap_local_host_name').'/moas/login'; ?>"
		target="_blank" method="post">
		<input type="email" name="username" value="<?php echo get_option('mo_ldap_local_admin_email'); ?>" />
		<input type="text" name="redirectUrl" value="<?php echo get_option( 'mo_ldap_local_host_name').'/moas/initializepayment'; ?>" />
		<input type="text" name="requestOrigin" id="requestOrigin"  />
		</form>
		<script>
			function upgradeform(planType){
				//alert(planType);
				jQuery('#requestOrigin').val(planType);
				jQuery('#loginform').submit();
			}
		</script>
		<br>
		<h3>Steps to upgrade to premium plugin -</h3>
		<p>1. You will be redirected to miniOrange Login Console. Enter your password with which you created an account with us. After that you will be redirected to payment page.</p>
		<p>2. Enter you card details and complete the payment. On successful payment completion, you will see the link to download the premium plugin.</p>
		<p>3. Once you download the premium plugin, just unzip it and replace the folder with existing plugin. </p>
		<b>Note: Do not delete the plugin from the Wordpress Admin Panel and upload the plugin using zip. Your saved settings will get lost.</b>
		<p>4. From this point on, do not update the plugin from the Wordpress store. We will notify you when we upload a new version of the plugin.</p>


		<h3>* Integrated Windows Authentication - If you use this plugin, access from a domain joined machine will still prompt you for credentials. If you want auto-login into the WordPress website from a domain joined machine, you need to setup and configure the <a href="https://wordpress.org/plugins/miniorange-windows-single-sign-on/">Windows SSO Plugin</a>.
		<h3>** Multiple LDAP Support - We have a version of the premium plugin which supports configuration of multiple LDAP Servers. Please contact us if you require this functionality.</h3>
		<h3>*** End to End Ldap Integration - We will setup a conference and do end to end configuration for you. We provide services to do the configuration on your behalf. </h3>
    <h3>**** Integration with other plugins - We have customized plugins which work with other WordPress plugins such as BuddyPress, WP User Manager, Gravity Forms etc. Please contact us if you require such integrations.</h3>
		<h3>***** Sync LDAP Profile with WordPress -  We have customized plugins which support sync with different LDAP implementations. Please contact us if you require such integrations.</h3>
		<h3>****** Outbound LDAP password sync - We have customized plugins to support password synchronization with LDAP. Please contact us if you require such integrations.</h3>
		<h3>******** Brute Force Protection - We have a <a href="https://wordpress.org/plugins/wp-security-pro/">Brute Force Plugin</a> for brute force protection. Please install the Brute Force plugin to get that functionality.</h3>
		<h3>********* Failed attempt notification - We have customized plugins for failed attempt notification. Please contact us if you require such integrations.</h3>
		<h3>********** Profile Photos Sync - We have customized plugins for syncing profile photos. Please contact us if you require such integrations.</h3>
		<h3>*********** NTLM SSO - We have customized plugins for NTLM SSO. Please contact us if you want this functionality.</h3>

		<h3>10 Days Return Policy -</h3>
		<p>At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved, we will refund the whole amount within 10 days of the purchase. Please email us at <a href="mailto:info@miniorange.com">info@miniorange.com</a> for any queries regarding the return policy.<br><br>
If you have any doubts regarding the licensing plans, you can mail us at <a href="mailto:info@miniorange.com">info@miniorange.com</a> or submit a query using the support form.</p>
		<br>




		</div>

	<?php }


?>
