<?php 

# We destroy existing session
if (session_id() != "") {
session_destroy();
}

# Start a session
session_start();

# Unset these variables so isset() works
# but don't erase session and lose login_attempts

unset($_SESSION['Username']);
unset($_SESSION['Password']);
$_SESSION['login']="true";
# include global regex routines, these
# have a habit of needing tweaking later
include('includes/regex.php');
include('config/config.php');

# Securily validate all input now
	$VAR = new regex;
	$feedback_array = $VAR->regex_textonly(array('Username' => $_REQUEST['username'],
								     'Password' => $_REQUEST['password']
								     ));

# Check we have Username and Password
if ((isset($_REQUEST['username']))&&(!isset($_REQUEST['password']))) {
	$_SESSION['feedback_array']['loginerror'] = "Please provide a password<br>";
}

#if ((!isset($_SESSION['VAR']->get('Username')))&&(!isset($_SESSION['VAR']->get('Password')))) {
if (($VAR->get('Username') == "")||($VAR->get('Password') == "")) {
#print "INITIAL";
	# Initial login screen
	htmlout();
} else {
	# We've got what we need, lets auth.
#print "GUNNA AUTH";

	$username  = $VAR->get('Username');
	$ldap_user = "uid=$username,OU=users,DC=jcu,DC=edu,DC=au";
	$ldap_pass = $VAR->get('Password');

#   $ldap_user = "uid=$username,OU=users,DC=jcu,DC=edu,DC=au";

	$ad = ldap_connect("ldap.jcu.edu.au");
	if ($ad === FALSE){
			# FIXME Seems impossible for this to fail...
        	$_SESSION['feedback_array']['loginerror'] = "Could not connect to LDAP Server<br>";
			htmlout();
	}else{
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
		}
		if (($bound === FALSE)||($row->access != 1)){
        		ldap_close($ad);
        		$_SESSION['feedback_array']['loginerror'] = "Username/Password not correct, please try again<br>";
			htmlout();
		}else{
#			#$_SESSION['feedback_array']['loginerror'] = "WELCOME";


			

			$_SESSION['login']="true";
			$_SESSION['Username'] = $VAR->get('Username');
			include("includes/gui_top.html");
			include("includes/gui_main_login_welcome.html");
#			print "<center>Welcome to Digital Library</center>";
			#htmlout();
		}
	}
}

function htmlout() {
	include("includes/gui_top_login.html");
	include("includes/gui_main_login.html");
}
?>
