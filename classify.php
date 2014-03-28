<?php
/*
 * Created on Aug 28, 2005
 */

session_start();

# Check user is authenticated
if ($_SESSION['login'] != "true") {
	include("includes/gui_top_login.html");
	include("includes/gui_main_login.html");
	exit();
}

include('config/config.php');
include('includes/regex.php');

# Securily validate all input now
$VAR = new regex;
$_SESSION['feedback_array'] = $VAR->regex_textonly(array(
							'id' 		=> $_REQUEST['id'],
							'Md5' 		=> $_REQUEST['md5'],									 
							'Classifiation' => $_REQUEST['classifiation'],
							'Keywords'      => $_REQUEST['keywords'],
							'Group'         => $_REQUEST['group'],
							'Date'          => $_REQUEST['date'],
							'Title'         => $_REQUEST['title'],
							'Filename'      => $_REQUEST['filename'],
							'Author'        => $_REQUEST['author'],
							'RelatedAuthor' => $_REQUEST['related_author']
								     ));

if (empty($_REQUEST['initial'])) {
	# Initial login screen
print ":".$_SESSION['login'].$_SESSION['VAR'].$_SESSION['login_attempts'].":";
#	htmlout();
} else {
	$link = mysql_pconnect($config_server, $config_user, $config_pass);
	if (!$link) {
   		print mysql_error();
	} else {
		mysql_select_db($config_db);
		// We only have id if its a current entry being updated.
		if ($VAR->get('id') != "") {
			mysql_query("update documents set filename='".$VAR->get('Filename')."', title='".$VAR->get('Title')."', author='".$VAR->get('Author')."', related_author='".$VAR->get('RelatedAuthor')."', keywords='".$VAR->get('Keywords')."', studygroup='".$VAR->get('Group')."', classification='".$VAR->get('Classifiation')."', publicationdate='".$VAR->get('Date')."' where documentid =".$VAR->get('id'));
			include("includes/gui_top.html");
			include("includes/gui_main_view.html");			
			exit();
		}

		$result = mysql_query("select * from documents where md5='".$VAR->get('Md5')."' limit 1");
		$row = mysql_fetch_object($result);
		
		if (!empty($row->filename)) {
			include("includes/gui_top.html");
   			print "<table width='80%' align='center''><tr><td>Exists with name ".$row->filename;
			print "</td></tr><tr><td>
                        <a href='get.php?md5=".$row->md5."&filename=".$row->filename."'>View file</a>
			<a href='upload.php?initial=false&id=".$row->documentid."'>Edit file</a>";
			exit();
		} else {
			mysql_query("INSERT INTO documents(filename,md5,title,author,related_author,keywords,studygroup,classification,publicationdate) VALUES('".$VAR->get('Filename')."','".$VAR->get('Md5')."','".$VAR->get('Title')."','".$VAR->get('Author')."','".$VAR->get('RelatedAuthor')."','".$VAR->get('Keywords')."','".$VAR->get('Group')."','".$VAR->get('Classifiation')."','".$VAR->get('Date')."')");
			#print mysql_error();
			$feedback = "Upload completed, next file?";
			include("includes/gui_top.html");
			include("includes/gui_main_upload.html");
		}
	}
	if (!empty($result->Filename)) {
		mysql_free_result($result);
	}
}

function htmlout() {
	$feedback = "Error, redrawing, dont do that again";
	include("includes/gui_top.html");	
	include("includes/gui_main_classify.php");
}

?>
