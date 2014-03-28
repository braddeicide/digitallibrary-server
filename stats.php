<?php
/*
 * Created on 4/10/2005
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

include('config/config.php');
include('includes/regex.php');

$stats = array();
$link = mysql_pconnect($config_server, $config_user, $config_pass);
if (!$link) {
   	print mysql_error();
}
mysql_select_db($config_db);

$sqlQuery = "select count(documentid) as c from documents";
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);
$stats[0] = $row->c; 

$sqlQuery = "select count(groupname) as c from groups";
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);
$stats[1] = $row->c;

$sqlQuery = "select author, count(author) as c from documents  where author !='' group by author limit 1";
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);
$stats[2] = $row->author;

$sqlQuery = "select studygroup, count(studygroup) as c from documents group by studygroup limit 1";
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);
$stats[3] = $row->studygroup;

$sqlQuery = "select title from documents order by documentid desc limit 5";
$result = mysql_query($sqlQuery);
$num=mysql_num_rows($result);
$loop=5;

if ($num < 5)
	$loop = $num;

for ($i=0; $i<$loop; $i++) 
	$stats[$i+4] = mysql_result($result,$i,"title");

include('includes/gui_top.html');
include('includes/gui_top_stats.php');

?>
