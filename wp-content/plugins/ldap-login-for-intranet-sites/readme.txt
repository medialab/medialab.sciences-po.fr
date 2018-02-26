=== Active Directory Integration / LDAP Integration ===
Contributors: miniOrange
Donate link: http://miniorange.com
Tags: active directory, ldap, authentication, ldap authentication, active directory integration, AD, ldap login, ldap sso, AD sso, AD authentication, active directory authentication, ldap single sign on, ad single sign on, ad login, active directory single sign on, openldap login, login form, user login, login, WordPress login,adfs
Requires at least: 2.0.2
Tested up to: 4.9
Stable tag: 2.7.43
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Active Directory Integration for Intranet sites login using credentials stored in Active Directory, OpenLDAP and other LDAP. ACTIVE SUPPORT.

== Description ==

Active Directory Integration for Intranet sites plugin provides login to WordPress using credentials stored in your LDAP Server. It allows users to authenticate against various LDAP implementations like Microsoft Active Directory, OpenLDAP and other directory systems. User information is in sync with the information in LDAP. This plugin is free to use under the GNU/GPLv2 license. If you wish to use enhanced features, then there is a provision to upgrade as well. There is also a provision to use our services to deploy and configure this plugin.

= Features :- =

*	Login to WordPress using your LDAP credentials ( Additionally login with WordPress credentials supported if enabled )
*	Automatic user registration after login if the user is not already registered with your site.
*	LDAP groups to WordPress Users Role Mapping.
*	Keep user profile information in sync with LDAP.
*	Define custom user profile information to be retrieved from LDAP. eg: phone number, department etc. [PREMIUM]
*	Uses LDAP or LDAPS for secure connection to your LDAP Server.
*	Can authenticate users against multiple search bases. [PREMIUM]
*	Can authenticate users against multiple user attributes like uid, cn, mail, sAMAccountName.
*	Test connection to your LDAP server.
*	Test authentication using credentials stored in your LDAP server.
*	Ability to test against demo LDAP server and demo credentials.
*	You will need to install PHP LDAP extension in WordPress.
*	No need for a public IP address or FQDN for your LDAP.
*	Will get support if you contact miniOrange at info@miniorange.com.
*	Fallback to local password in case LDAP is unreacheable. [PREMIUM]
*	Multisite support. [PREMIUM]
* Integrations with plugins like Buddypress, WP User Manager Gravity Forms etc available. Contact Us if you need such integrations. [PREMIUM]
* Sync LDAP Profile with WordPress. Contact us if you need such features. [PREMIUM]
* Outbound LDAP Password Sync. Contact us if you require password sync with LDAP. [PREMIUM]
* Brute Force Protection. Contact us if you require this functionality. [PREMIUM]
* User/Admin email notification on failed logon attempt. Contact us in case you require this functionality. [PREMIUM]
* Sync Profile Photos with WordPress. Contact us if you require this functionality. [PREMIUM]
* If you are looking for Single-Sign-On in addition to authentication with AD/LDAP and do not have any Identity Provider Yet. You can try out <a href="https://idp.miniorange.com">miniOrange On-Premise IdP</a>.


= Do you want support? =
Please email us at info@miniorange.com or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>

== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`
2. Search for `Active Directory Integration for Intranet sites`. Find and Install `Active Directory Integration for Intranet sites`
3. Activate the plugin from your Plugins page

= From WordPress.org =
1. Download Active Directory Integration for Intranet sites.
2. Unzip and upload the `ldap-login-for-intranet-sites` directory to your `/wp-content/plugins/` directory.
3. Activate Active Directory Integration for Intranet sites from your Plugins page.

= Once Activated =
1. Go to `Settings-> LDAP Login Config`, and follow the instructions.
2. Click on `Save`

Make sure that if there is a firewall, you `OPEN THE FIREWALL` to allow incoming requests to your LDAP from your WordPress Server IP and open port 389(636 for SSL or ldaps).

== Frequently Asked Questions ==

= How should I enter my LDAP configuration? I only see Register with miniOrange. =
Our very simple and easy registration lets you register with miniOrange. Once you have registered with a valid email-address and phone number, you will be able to add your LDAP configuration.

= I am not able to get the configuration right. =
Make sure that if there is a firewall, you `OPEN THE FIREWALL` to allow incoming requests to your LDAP from your WordPress Server IP and open port 389(636 for SSL or ldaps). For further help please click on the Troubleshooting tab where you can find detailed description for each configuration. If that does not help, please check the format of example settings in `Example LDAP Configuration` tab.

= I am locked out of my account and can't login with either my WordPress credentials or LDAP credentials. What should I do? =
Firstly, please check if the `user you are trying to login with` exists in your WordPress. To unlock yourself, rename ldap-login-for-intranet-sites plugin name. You will be able to login with your WordPress credentials. After logging in, rename the plugin back to ldap-login-for-intranet-sites. If the problem persists, `activate, deactivate and again activate` the plugin.

= For support or troubleshooting help =
Please email us at info@miniorange.com or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>.

We can add the provision of user management such as creating users not present in WordPress from LDAP Server, adding users, editing users and so on. For further details, please email us at info@miniorange.com or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>.

== Screenshots ==

1. Configure LDAP plugin
2. LDAP Groups to WordPress Users Role Mapping
3. User Attributes Mapping between LDAP and WP
4. Example LDAP Configuration

== Changelog ==

= 2.7.43 =
On-premise IdP information

= 2.7.42 =
WordPress 4.9 Compatibility

= 2.7.4 =
Fix for login with username/email

= 2.7.3 =
Additional feature links.

= 2.7.2 =
Licensing fixes.

= 2.7.1 =
Activation warning fix. Basic registration fields required for upgrade.

= 2.7 =
Registration removal, role mapping fixes and username attribute configurable.

= 2.6.6 =
Updating Plugin Title

= 2.6.5 =
Licensing fix

= 2.6.4 =
Name fixes

= 2.6.2 =
Name changed

= 2.6.1 =
Added TLS support

= 2.5.8 =
Increased priority for authentication hook

= 2.5.7 =
Licensing fixes

= 2.5.6 =
WordPress 4.6 Compatibility


= 2.5.5 =
Added option to authenticate Administrators from both LDAP and WordPress

= 2.5.4 =
More page fixes


= 2.5.3 =
Page fixes

= 2.5.2 =
Registration fixes

= 2.5.1 =
*	UI improvement and fix for WP 4.5

= 2.5 =
Added more descriptive error messages and licensing plans updated.

= 2.3 =
Support for Integrated Windows Authentication - contact info@miniorange.com if interested

= 2.2 =
+Added alternate verification method for user activation.

= 2.1 =
+Minor Bug fixes.

= 2.0 =
Attribute Mapping and Role Mapping Bug fixes and Enhancement.

= 1.9 =
Attribute Mapping bug fixes

= 1.8 =
Role Mapping Bug fixes

= 1.7 =
Fallback to local password in case LDAP server is unreacheable.

= 1.6 =
Added attribute mapping and custom profile fields from LDAP.

= 1.5 =
Added mutiple role support in WP users to LDAP Group Role Mapping.

= 1.4 =
Improved encryption to support special characters.

= 1.3 =
Enhanced Usability and UI for the plugin.

= 1.2 =
Added LDAP groups to WordPress Users Role Mapping

= 1.1 =
Enhanced Troubleshooting

= 1.0 =
* this is the first release.

== Upgrade Notice ==

= 2.7.43 =
On-premise IdP information

= 2.7.42 =
WordPress 4.9 Compatibility

= 2.7.4 =
Fix for login with username/email

= 2.7.3 =
Additional feature links.

= 2.7.2 =
Licensing fixes.

= 2.7.1 =
Activation warning fix. Basic registration fields required for upgrade.

= 2.7 =
Registration removal, role mapping fixes and username attribute configurable.

= 2.6.5 =
Licensing fix

= 2.6.4 =
Name fixes

= 2.6.2 =
Name changed

= 2.6.1 =
Added TLS support

= 2.5.8 =
Increased priority for authentication hook

= 2.5.7 =
Licensing fixes

= 2.5.6 =
WordPress 4.6 Compatibility

= 2.5.5 =
Added option to authenticate Administrators from both LDAP and WordPress

= 2.5.4 =
More page fixes

= 2.5.3 =
Page fixes

= 2.5.2 =
Registration fixes

= 2.5.1 =
*	UI improvement and fix for WP 4.5

= 2.5 =
Added more descriptive error messages and licensing plans updated.

= 2.3 =
Support for Integrated Windows Authentication - contact info@miniorange.com if interested

= 2.2 =
+Added alternate verification method for user activation.

= 2.1 =
+Minor Bug fixes.

= 2.0 =
Attribute Mapping and Role Mapping Bug fixes and Enhancement.

= 1.9 =
Attribute Mapping bug fixes

= 1.8 =
Role Mapping Bug fixes

= 1.7 =
Fallback to local password in case LDAP server is unreacheable.

= 1.6 =
Added attribute mapping and custom profile fields from LDAP .

= 1.5 =
Added mutiple role support in WP users to LDAP Group Role Mapping .

= 1.4 =
Improved encryption to support special characters.

= 1.3 =
Enhanced Usability and UI for the plugin.

= 1.2 =
Added LDAP groups to WordPress Users Role Mapping

= 1.1 =
Enhanced Troubleshooting

= 1.0 =
First version of plugin.
