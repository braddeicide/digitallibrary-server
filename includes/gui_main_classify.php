        <td><p><?php print $feedback ?></p>
          <p>&nbsp;</p>
          <table width="300"  border="2" align="center" cellpadding="5" cellspacing="0">
			<form action="classify.php" method="post">
            <tr>
              <td><table width="600" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>Origional Filename</td>
					<td>
					  <input name="md5"  type="hidden" value="<?php print $md5; ?>">
					  <input name="initial"  type="hidden" value="false">
                      <input name="filename" type="text" maxlength="100" value="<?php print $_FILES['file']['name'].$info['Filename']; ?>">
                    </td>
				  </tr>

              <tr>
                  <td>Author</td>
                    <td>
                          <input name="author" type="text" maxlength="50" value="<?php print $info['Author']; ?>">
				  </td>
              </tr>
              <tr>
                  <td>Related Author</td>
                    <td>
                          <input name="related_author" type="text" maxlength="100" value="<?php print $info['Related_Author']; ?>">
 				  </td>
              </tr>
              <tr>
                  <td>Title</td>
                    <td>
                          <input name="title" type="text" maxlength="50" value="<?php print $info['Title']; ?>" size="30">
				  </td>
              </tr>
              <tr>
                  <td>Publication Date</td>
					<td>
                          <select name="date">
							<option><?php print $info['CreationDate']; ?></option>
                            <option>1980</option>
                            <option>1981</option>
                            <option>1982</option>
                            <option>1983</option>
                            <option>1984</option>
                            <option>1985</option>
                            <option>1986</option>
                            <option>1987</option>
                            <option>1988</option>
                            <option>1989</option>
                            <option>1990</option>
                            <option>1991</option>
                            <option>1992</option>
                            <option>1993</option>
                            <option>1994</option>
                            <option>1995</option>
                            <option>1996</option>
                            <option>1997</option>
                            <option>1998</option>
                            <option>1999</option>
                            <option>2000</option>
                            <option>2001</option>
                            <option>2002</option>
                            <option>2003</option>
                            <option>2004</option>
                            <option>2005</option>
                            <option>2006</option>
                            <option>2007</option>
                            <option>2008</option>
                            <option>2009</option>
                            <option>2010</option>
                          </select>
				  </td>
              </tr>
              <tr>
                  <td>Research Group</td>
                    <td>
                          <select name="group">
<?php
//include('../config/config.php');
$link = mysql_pconnect($config_server, $config_user, $config_pass);
if (!$link) {
   	print mysql_error();
} else {
	$SqlQuery="select groupname from groups";
	mysql_select_db("digitallibrary");
	$result = mysql_query($SqlQuery);
	//print $SqlQuery;
	$num=mysql_num_rows($result);
				
	for ($i=0; $i<$num; $i++) {
		print "<option"; 
		if(mysql_result($result,$i,"groupname") == $info['StudyGroup']){print " selected ";}
		print ">".mysql_result($result,$i,"groupname")."</option>\n";
	}
}

?>
                          </select>
				  </td>
              </tr>
              <tr>
                  <td>Keywords</td>
                    <td>
                          <input name="keywords" type="text" maxlength="100" value="<?php print $info['Keywords']; ?>">
				  </td>
              </tr>
              <tr>
                  <td>Classiciation</td>
				  <td>
                          <select name="classifiation">
                            <option<?php if ($info['Classification'] == "General") print " selected"; ?>>General</option>
                            <option<?php if ($info['Classification'] == "Thesis") print " selected"; ?>>Thesis</option>
                            <option<?php if ($info['Classification'] == "Journal") print " selected"; ?>>Journal</option>
                            <option<?php if ($info['Classification'] == "Technical Report") print " selected"; ?>>Technical Report</option>
                            <option<?php if ($info['Classification'] == "Book") print " selected"; ?>>Book</option>
                            <option<?php if ($info['Classification'] == "Book Chapter") print " selected"; ?>>Book Chapter</option>
                            <option<?php if ($info['Classification'] == "Documentation") print " selected"; ?>>Documentation</option>
                          </select>
				  </td>
              </tr>




                </table>
                  <div align="right"><br>
                      <input type="submit" name="Submit" value="Submit">
                  </div>

                  <p align="left"></td>
            </tr>
          </table>
          <p>&nbsp;</p></td>
      </tr>
    </table>      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <input name="id" type="hidden" value="<?php print $VAR->get('id'); ?>">
      <form name="form1" method="post" action="">

        <p align="left">
      </form>
      <p align="left">      </td>
  </tr>
</table>
