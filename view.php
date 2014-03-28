<?php
/*
 * Created on 14/09/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

session_start();

# Check user is authenticated
if ($_SESSION['login'] != "true") {
	include('config/config.php');
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
$feedback_array = $VAR->regex_textonly(array('	textContainsAuthor'     => $_REQUEST['textContainsAuthor'],
						'textStartsAuthor'      => $_REQUEST['textStartsAuthor'],
						'textEndsAuthor'        => $_REQUEST['textEndsAuthor'],
						'textContainsRelated'   => $_REQUEST['textContainsRelated'],
						'textStartsRelated'     => $_REQUEST['textStartsRelated'],
						'textEndsRelated'       => $_REQUEST['textEndsRelated'],
						'textContainsTitle'     => $_REQUEST['textContainsTitle'],
						'textStartsTitle'       => $_REQUEST['textStartsTitle'],
						'textEndsTitle'         => $_REQUEST['textEndsTitle'],
						'selectStartsDate'      => $_REQUEST['selectStartsDate'],
						'selectEndsDate'        => $_REQUEST['selectEndsDate'],
						'selectGroup'           => $_REQUEST['selectGroup'],
						'textStartsGroup'       => $_REQUEST['textStartsGroup'],
						'textEndsGroup'         => $_REQUEST['textEndsGroup'],
						'textContainsKeywords'  => $_REQUEST['textContainsKeywords'],
						'textNotKeywords'       => $_REQUEST['textNotKeywords'],
						'selectClassification'  => $_REQUEST['selectClassification'],
						'id'			=> $_REQUEST['id']
					));

if (empty($_REQUEST['initial'])) {
	# Initial login screen
	htmlout();
} else {

$_SESSION['checkAuthor'] = $_REQUEST['checkAuthor'];
$_SESSION['textContainsAuthor'] = $_REQUEST['textContainsAuthor'];
$_SESSION['textStartsAuthor'] = $_REQUEST['textStartsAuthor'];
$_SESSION['textEndsAuthor'] = $_REQUEST['textEndsAuthor'];
$_SESSION['checkRelated'] = $_REQUEST['checkRelated'];
$_SESSION['textContainsRelated'] = $_REQUEST['textContainsRelated'];
$_SESSION['textStartsRelated'] = $_REQUEST['textStartsRelated'];
$_SESSION['textEndsRelated'] = $_REQUEST['textEndsRelated'];
$_SESSION['checkTitle'] = $_REQUEST['checkTitle'];
$_SESSION['textContainsTitle'] = $_REQUEST['textContainsTitle'];
$_SESSION['textStartsTitle'] = $_REQUEST['textStartsTitle'];
$_SESSION['textEndsTitle'] = $_REQUEST['textEndsTitle'];
$_SESSION['checkDate'] = $_REQUEST['checkDate'];
$_SESSION['selectStartsDate'] = $_REQUEST['selectStartsDate'];
$_SESSION['selectEndsDate'] = $_REQUEST['selectEndsDate'];
$_SESSION['checkGroup'] = $_REQUEST['checkGroup'];
$_SESSION['selectGroup'] = $_REQUEST['selectGroup'];
$_SESSION['textStartsGroup'] = $_REQUEST['textStartsGroup'];
$_SESSION['textEndsGroup'] = $_REQUEST['textEndsGroup'];
$_SESSION['checkKeywords'] = $_REQUEST['checkKeywords'];
$_SESSION['textContainsKeywords'] = $_REQUEST['textContainsKeywords'];
$_SESSION['textNotKeywords'] = $_REQUEST['textNotKeywords'];
$_SESSION['checkClassification'] = $_REQUEST['checkClassification'];
$_SESSION['selectClassification'] = $_REQUEST['selectClassification'];

// Get permission values
$link = mysql_pconnect($config_server, $config_user, $config_pass);
if (!$link) {
   	print mysql_error();
}
$sqlQuery = "select access, upload, admin from admin where username='".$_SESSION['Username']."'";	
mysql_select_db($config_db);
$result = mysql_query($sqlQuery);
$row = mysql_fetch_object($result);

if ($row->access != 1){
	include("includes/gui_top.html");
	print "<div align='center'> Sorry, you do not have permission to delete</div>";
exit();
}

	if ($VAR->get('id') != "") {
		//We only get ID if we have confirmed a delete

	if (($row->access)&&($VAR->get('id') != '')) {
		// Select filename
		$sqlQuery = "select md5 from documents where documentid='".$VAR->get('id')."'";
		$result = mysql_query($sqlQuery);
		$row2 = mysql_fetch_object($result);
		// delete from database
		$sqlQuery = "delete from documents where documentid='".$VAR->get('id')."'";
		mysql_query($sqlQuery);
		// Testing due to IE weirdness, deletes twice. firefox is fine.
		if ($row2->md5 != "") {
			// delete from filesystem
			unlink($config_uploadDir.$row2->md5);
			$_SESSION['deleteFeedback'] = "Successfully deleted file";
		}
	}
}

	
	// Generate the SQL query
	// The "now()" is so each string append 
	// can contain "and where" instead detecting if 
	// we are first and dropping the "and" each time.
	$SqlQuery = "select * from documents where now()";

	if (!empty($_REQUEST['checkAuthor'])) {
		if (!empty($_REQUEST['textContainsAuthor'])) {
			$SqlQuery .= " and author like '%".$VAR->get('textContainsAuthor')."%'";
		}
		if (!empty($_REQUEST['textStartsAuthor'])) {
			$SqlQuery .= " and author like '".$VAR->get('textStartsAuthor')."%'";
		}
		if (!empty($_REQUEST['textEndsAuthor'])) {
			$SqlQuery .= " and author like '%".$VAR->get('textEndsAuthor')."'";
		}
	}
	if (!empty($_REQUEST['checkRelated'])) {
		if (!empty($_REQUEST['textContainsRelated'])) {
			$SqlQuery .= " and related_author like '%".$VAR->get('textContainsRelated')."%'";
		}
		if (!empty($_REQUEST['textStartsRelated'])) {
			$SqlQuery .= " and related_author like '".$VAR->get('textStartsRelated')."%'";
		}
		if (!empty($_REQUEST['textEndsRelated'])) {
			$SqlQuery .= " and related_author like '%".$VAR->get('textEndsRelated')."'";
		}
	}
	if (!empty($_REQUEST['checkTitle'])) {
		if (!empty($_REQUEST['textContainsTitle'])) {
			$SqlQuery .= " and title like '%".$VAR->get('textContainsTitle')."%'";
		}
		if (!empty($_REQUEST['textStartsTitle'])) {
			$SqlQuery .= " and title like '".$VAR->get('textStartsTitle')."%'";
		}
		if (!empty($_REQUEST['textEndsTitle'])) {
			$SqlQuery .= " and title like '%".$VAR->get('textEndsTitle')."'";
		}
	}
	if (!empty($_REQUEST['checkDate'])) {
		if ($_REQUEST['selectStartsDate'] != "any") {
			$SqlQuery .= " and publicationdate > '".$VAR->get('selectStartsDate')."'";
			if ($_REQUEST['selectEndsDate'] == "any") {
				$SqlQuery .= " and publicationdate < '9999'";
			}
		}
		if ($_REQUEST['selectEndsDate'] != "any") {
			$SqlQuery .= " and publicationdate < '".$VAR->get('selectEndsDate')."'";
			if ($_REQUEST['selectStartsDate'] == "any") {
				$SqlQuery .= " and publicationdate > '0000'";
			}
		}
	}

	if (!empty($_REQUEST['checkGroup'])) {
		if (!empty($_REQUEST['selectGroup'])) {
			$SqlQuery .= " and studygroup = '".$VAR->get('selectGroup')."'";
		}
		if (!empty($_REQUEST['textStartsGroup'])) {
			$SqlQuery .= " and studygroup like '".$VAR->get('textStartsGroup')."%'";
		}
		if (!empty($_REQUEST['textEndsGroup'])) {
			$SqlQuery .= " and studygroup like '%".$VAR->get('textEndsGroup')."'";
		}
	}

	if (!empty($_REQUEST['checkKeywords'])) {
		if (!empty($_REQUEST['textContainsKeywords'])) {
			$SqlQuery .= " and keywords like '%".$VAR->get('textContainsKeywords')."%'";
		}
		if (!empty($_REQUEST['textNotKeywords'])) {
			$SqlQuery .= " and keywords not like '%".$VAR->get('textNotKeywords')."%'";
		}
	}

	if (!empty($_REQUEST['checkClassification'])) {
		if (!empty($_REQUEST['selectClassification'])) {
			$SqlQuery .= " and classification = '".$VAR->get('selectClassification')."'";
		}
	}
	
	// Run the query
	$link = mysql_pconnect($config_server, $config_user, $config_pass);
	if (!$link) {
   		print mysql_error();
	} else {
		mysql_select_db($config_db);
		$result = mysql_query($SqlQuery);
		//print $SqlQuery;
		$num=mysql_num_rows($result);
		$i=0;

		// Here come the results
		include("includes/gui_top.html");
		include("includes/gui_main_view.html");
		

		?>
            <table width="98%" border="1" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td width="2%"><div align="center">ID</div></td>
                  <td width="6%"><div align="center">Author(s)</div></td>
                  <td width="14%"><div align="center">Related Author(s)</div></td>
                  <td width="46%"><div align="center">Title</div></td>
                  <td width="7%"><div align="center">Publication Year </div></td>
                  <td width="9%"><div align="center">Research Group </div></td>
                  <td width="10%"><div align="center">Classiciation</div></td>
                  <td width="6%">&nbsp;</td>

                </tr>
		<?php

		while ($i < $num) {
			// Php parsed pages will not print a " directly after a ? >
			// This looks like a PHP bug that may be fixed later, i switched to ''

			print "<tr>\n";
			print "     <td><div align='center'> <a href='get.php?md5=".mysql_result($result,$i,"md5")."' target='_new'>".$i."</div></a></td>";
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"author") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"author");} else {print "&nbsp;";} print "</div></a></td>\n";
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"related_author") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"related_author");} else {print "&nbsp;";} print "</div></a></td>\n";
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"title") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"title");} else {print "&nbsp;";} print "</div></a></td>\n";						
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"publicationdate") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"publicationdate");} else {print "&nbsp;";} print "</div></a></td>\n";
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"studygroup") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"studygroup");} else {print "&nbsp;";} print "</div></a></td>\n";
			print "     <td><div align='center'>"; if (mysql_result($result,$i,"classification") != "") { print "<a href='get.php?md5=".mysql_result($result,$i,"md5")."&filename=".mysql_result($result,$i,"filename")."' target='_new'>".mysql_result($result,$i,"classification");} else {print "&nbsp;";} print "</div></a></td>\n";									
#			print "		<td><div align='center'>(modify)<a href='delete.php?id=".(mysql_result($result,$i,"documentid"))."' target='_new' coords='36,155,168,276' >(delete)</a></td>";
			print "		<td><div align='center'><a href='upload.php?initial=false&id=".(mysql_result($result,$i,"documentid"))."'>(modify)</a><a href=\"javascript:void(0)\"><img src=\"images/delete.gif\" border=\"0\" onClick=\"window.open('delete.php?id=".(mysql_result($result,$i,"documentid"))."','new','width=400,height=100')\"></a></td>";
			print "</tr>\n";
		$i++;
		}
	
			if ($num == 0)
			print "<tr><td colspan='8'><div align='center'>No Documents Found</div></td></tr>";

	?>
		  </table>
	    </tr>
      </table>
    </div>      
  </td></tr>
</table>
<?php
	}	
}

function htmlout() {
	include("includes/gui_top.html");
	include("includes/gui_main_view.html");
}	
?>
