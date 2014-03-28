<?php
/*
 * Created on 21/09/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();

# Check user is authenticated
if (($_SESSION['login'] != "true")&&(empty($_REQUEST['username']))) {
	include("includes/gui_top_login.html");
	include("includes/gui_main_login.html");
	exit();
}

include('config/config.php');
include('includes/regex.php');

if (empty($_REQUEST['md5'])) {
	# This should never happen
	print "Error, request file not specified";
}
else {

	$VAR = new regex;
	$feedback_array = $VAR->regex_textonly(array(	'md5'            => $_REQUEST['md5'],
							'filename'       => $_REQUEST['filename'],
        	                                        'Username'              => $_REQUEST['username'],
	                                                'Password'              => $_REQUEST['password']
							));
	

	# PHP Sessions do not work with java, so JAVA must auth every time it makes
	# a call.  So if there is a username and password, its the JAVA client.

	if ($VAR->get('Username') != "") {
        	$username  = $VAR->get('Username');
        	$ldap_user = "uid=$username,OU=users,DC=jcu,DC=edu,DC=au";
        	$ldap_pass = $VAR->get('Password');
        	$ad = ldap_connect("ldap.jcu.edu.au");
        	if ($ad !== FALSE){
                	ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
                	$bound = @ldap_bind($ad, $ldap_user, $ldap_pass);
                	$link = mysql_pconnect($config_server, $config_user, $config_pass);
                	if (!$link) {
                        	print mysql_error();
                	} else {
                        	mysql_select_db($config_db);
                        	$sqlQuery = "select access from admin where username='".$VAR->get('Username')."'";
                        	$result = mysql_query($sqlQuery);
                        	$row = mysql_fetch_object($result);
                        	if (($bound === FALSE)||($row->access != 1)){
                                	print "Auth error";
                        	} else {
                                	$Username = $VAR->get('Username');
                        	}
                	}
        	} else {
                	print "sorry, auth fail";
                	exit();
       		}
	} else {
	$Username = $_SESSION['Username'];
	}

	# Open the file
	$location = $config_uploadDir.$VAR->get('md5');
	$handle = fopen($location, "rb");

	if (empty($handle)) {
		# In case PHP errors are not sent to the browser, good idea btw.
		print "Error, unable to open file ".$location;
	}
	
	# gather statistics
	$fstat = fstat($handle);

	if (empty($fstat[7]))
		exit();
	
	$contents = '';
	while (!feof($handle)) {
	  $contents .= fread($handle, 8192);
	}
	fclose($handle);

	header('Accept-Ranges: bytes');
	header("Content-Length: ".$fstat[7]);        // size of the file
	header('Keep-Alive: timeout=15, max=100');
	header('Content-type: Application/x-zip');
	header("Content-Disposition: attachment; filename=\"".$VAR->get('filename')."\"");
	
	print $contents;
}	
?>
