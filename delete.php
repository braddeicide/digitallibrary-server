<?php
/*
 * Created on 4/10/2005
 *
 * This file is responsible for deleting files from the
 * database and hard drive
 * 
 * Note: By clients request, to delete users only need
 * access permission, this might be tightened later.
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

# Securily validate all input now
$VAR = new regex;
$feedback_array = $VAR->regex_textonly(array('id'     => $_REQUEST['id']
								     ));

$_SESSION['id'] = $VAR->get('id');
htmlout();
exit();


function htmlout(){
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	background-color: #F1FAFA;
}
-->
</style></head>

<body>
<div align="center" id="Layer1" style="visibility: visible;">Confirm delete</div>
   <form name="form1" method="post" action="view.php" target="DigitalLibrary" onSubmit="setTimeout('window.close()',2000)">
<input type="hidden" name="initial" value="false">
<input type="hidden" name="id" value="">
<div align="center">
<input type="submit" name="Submit2" value="Delete" onClick="document.form1.id.value='<?php print $_SESSION['id']; ?>'; document.getElementById('Layer1').innerHTML='Please wait'; document.form1.submit();">
&nbsp;&nbsp;     
<input type="submit" name="Submit" value="Cancel" onClick="window.close()">
 </div>
   </form>
   <p>&nbsp;   </p>
</body>
</html>


<?php	
}

?>