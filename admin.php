<?php
/*
 * Created on 27/09/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();

# Check user is authenticated
if ($_SESSION['login'] != "true") {
	include("includes/gui_top_login.html");
	include("includes/gui_main_login.html");
	exit();
}

# include global regex routines, these
# have a habit of needing tweaking later
include('config/config.php');
include('includes/regex.php');
include('includes/datamine.php');

$VAR = new regex;
$feedback_array = $VAR->regex_textonly(array('username'        	=> $_REQUEST['username'],
								             'addUser'         	=> $_REQUEST['addUser'],
								             'delete'          	=> $_REQUEST['delete'],
//								             'form'            	=> $_REQUEST['form'],
								             'groups'          	=> $_REQUEST['groups'],
								             'groupMembers'    	=> $_REQUEST['groupMembers'],
								             'createGroupText'	=> $_REQUEST['createGroupText'],
								             'access'			=> $_REQUEST['access'],
								             'upload' 			=> $_REQUEST['upload'],
								             'admin' 			=> $_REQUEST['admin'],
								             'nameUserText' 	=> $_REQUEST['nameUserText'],
											 'addUserText' 		=> $_REQUEST['addUserText'],
											 'userAction' 		=> $_REQUEST['userAction'],
											 'groupAction' 		=> $_REQUEST['groupAction'],
											 'auth'				=> $_REQUEST['auth']
								 	   ));

	include("includes/gui_top.html");

// Get permission values
$link = mysql_pconnect($config_server, $config_user, $config_pass);
if (!$link) {
   	print mysql_error();
}
$sqlQuery = "select access, upload, admin from admin where username='".$_SESSION['Username']."'";	
mysql_select_db($config_db);
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);

if ($row->admin != 1){
	print "<div align='center'> Sorry, you do not have permission to admin</div>";
exit();
}



if (empty($_REQUEST['initial'])) {
	# Initial screen
	include("includes/gui_main_admin.html");
} else {
	if ($VAR->get('auth') == "") {
	    $link = mysql_pconnect($config_server, $config_user, $config_pass);
		if (!$link) {
   			print mysql_error();
		}
		mysql_select_db($config_db);

		// Add user
		if ($VAR->get('userAction') == "userAdd") {
			// Looks like we're adding a user	

			//FIXME, check user is in domain...
			
            $SqlQuery="insert into admin(username,fullname,access,upload,admin) values('".$VAR->get('addUserText')."','".$VAR->get('nameUserText')."','".$VAR->get('access')."','".$VAR->get('upload')."','".$VAR->get('admin')."')";
			mysql_query($SqlQuery);
		}
		// Delete user
		if ($VAR->get('userAction') == "userDelete") {
			//Looks like we deleting the user
			$SqlQuery="delete from admin where username='".$VAR->get('username')."'";
			mysql_query($SqlQuery);
		}
		if ($VAR->get('userAction') == "userMod") {
			//Looks like we deleting the user
			$SqlQuery="update admin set access='".$VAR->get('access')."', upload='".$VAR->get('upload')."', admin='".$VAR->get('admin')."' where username='".$VAR->get('username')."'";
			mysql_query($SqlQuery);
		}

		if ($VAR->get('groupAction') == "createGroup") {
			$SqlQuery="insert into groups(groupname) values('".$VAR->get('createGroupText')."')";
			mysql_query($SqlQuery);
		}
		//Delete group
		if ($VAR->get('groupAction') == "deleteGroup") {
			$SqlQuery="delete from groups where groupname='".$VAR->get('groups')."'";
			mysql_query($SqlQuery);
		}
		//Add user to group
		if ($VAR->get('groupAction') == "AddUserToGroup") {

			$SqlQuery="select members from groups where groupname='".$VAR->get('groups')."'";
			$result = mysql_query($SqlQuery);
			$row = mysql_fetch_object($result);
			
			$tmpString = $row->members.":".$VAR->get('username');
			$tmpString2 = trim($tmpString, ":");
			
			$SqlQuery="update groups set members='".$tmpString2."' where groupname='".$VAR->get('groups')."'";
			mysql_query($SqlQuery);
		}			
		//Remove user from group
		if ($VAR->get('groupAction') == "RemoveUserFromGroup") {			
			$SqlQuery="select members from groups where groupname='".$VAR->get('groups')."'";
			$result = mysql_query($SqlQuery);
			$row = mysql_fetch_object($result);
			
			$membersArray = split(":",$row->members);

			foreach ($membersArray as $inside) {
				if ($inside != $VAR->get('groupMembers'))
					$tmpString = ":".$inside;
			}
			$tmpString2 = trim($tmpString,":");

			$SqlQuery="update groups set members='".$tmpString2."' where groupname='".$VAR->get('groups')."'";
			mysql_query($SqlQuery);
		}
	}
	include("includes/gui_main_admin.html");
	}
?>
