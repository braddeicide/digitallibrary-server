<?php 
# Handles uploading, showing classification, aswell as reclassification.


session_start();

# Check user is authenticated
if (($_SESSION['login'] != "true")&&(empty($_REQUEST['username']))) {
	include("includes/gui_top_login.html");
	include("includes/gui_main_login.html");
	exit();
}
# include global regex routines, these
# have a habit of needing tweaking later
include('config/config.php');
include('includes/regex.php');
include('includes/datamine.php');

# Securily validate all input now
$VAR = new regex;
$feedback_array = $VAR->regex_textonly(array('id'           => $_REQUEST['id'],
											 'Username'     => $_REQUEST['username'],
											 'Password'     => $_REQUEST['password']
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

# Get permission values
$link = mysql_pconnect($config_server, $config_user, $config_pass);
if (!$link) {
   	print mysql_error();
}
$sqlQuery = "select access, upload, admin from admin where username='".$Username."'";	
mysql_select_db($config_db);
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);

if ($row->upload != 1){
	include("includes/gui_top.html");
	print "<div align='center'> Sorry, you do not have permission to upload</div>".$Username;
exit();
}

if (empty($_REQUEST['initial'])) {
	# Initial login screen
	htmlout();
} else {
	if ($VAR->get(id) == "") {
		# id is only used for reclassification, this is an upload
		# Accept file uploads
		# FIXME Later i'll detect upload type here.
		$info['exists']=1;
		$testfor = array("Author","Related_Author","Title","Creator","Producer","CreationDate","Keywords");
	
		# We store file with its MD5 name, this way there will never be an
		# overwrite, and if there is it does not matter
		$md5 = md5_file($_FILES['file']['tmp_name']);
		# This is added to allow compatibility with Java version file repository. 
		# Windows has no idea what a file is without an extension, so we append the
		# extenstion to the filename
		preg_match("/.[0-9a-zA-Z\-\_]+$/i", $_FILES['file']['name'],$matches);
		$md5.= 	$matches[0];
		$uploadFile = $config_uploadDir . $md5;
	
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
			# File is valid, and was successfully uploaded
		   	# If its PDF we will look for usful information
			if (preg_match('/.pdf$/i',$_FILES['file']['name'])) {
				$tmp = new datamine();
				$info = $tmp->pdf($config_uploadDir . $md5,$testfor);
			}else {
				# More file compatibility later :)
			}
		}else {
			print "File upload failed!  Please report debugging info:\n";
		    print_r($_FILES);
		    print $_REQUEST;
			exit();
		}
	}else{

	$sqlQuery = "select * from documents where documentid='".$VAR->get('id')."'";
	mysql_select_db($config_db);
	$result = mysql_query($sqlQuery);
	$row = mysql_fetch_object($result);

	$info =array("Author"=>$row->author,
				"Related_Author"=>$row->related_author,
				"Title"=>$row->title,
				"StudyGroup"=>$row->studygroup,
				"Filename"=>$row->filename,
				"CreationDate"=>$row->publicationdate,
				"Classification"=>$row->classification,
				"Keywords"=>$row->keywords);
	}
	
	# Have all values now so we print classification page with our hints
	# FIXME only one file allowed at a time for now
    $feedback = "Files uploaded, please confirm classification criteria for searching.</p>
          <p>This can be completed later by browsing archive and selecting update icon on right.";
	include("includes/gui_top.html");
	include("includes/gui_main_classify.php");
	
}

function htmlout() {
	include("includes/gui_top.html");
	include("includes/gui_main_upload.html");
}
?>
