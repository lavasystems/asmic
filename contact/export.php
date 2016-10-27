<?php
// ** GET CONFIGURATION DATA **
    require_once('constants.inc');
    require_once(FILE_FUNCTIONS);
    require_once(FILE_CLASS_OPTIONS);
	include_once("local_config.php");
	require_once('../includes/functions.php');

	
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}

// ** OPEN CONNECTION TO THE DATABASE **
    $db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** EXPORT FORMATS **
	switch($_GET['format']) {

/********************************************************************************
 ** MYSQL DUMP FORMAT
 **
 ********************************************************************************/
		case "mysql":

			// FUNCTION DECLARATION
			function createInsertQuery($table) 
			{
				global $db_link;
				// Obtain the information from the table
				$result = mysql_query("SELECT * FROM " . $table, $db_link);
			    // Create the Insert Query
				while ($resultrow = mysql_fetch_row($result)) 
				{
					echo "INSERT INTO " . $table . " VALUES(";
					for ($i=0; $i < count($resultrow); $i++) 
					{
						if ($i != 0) 
						{	echo ",";	}
						echo (is_numeric($resultrow[$i]) ? "$resultrow[$i]" : "\"" . addslashes($resultrow[$i]) . "\""); // outputs numbers without quotes, strings with addslashes/double-quotes
					}
					echo ");\n";
				}
				// Clear the result from memory -- we don't need it anymore
				mysql_free_result($result);
			// end function
			}

			// OUTPUT
		    header("Content-type: text/plain");
			header("Content-disposition: attachment; filename=tab_mysql.txt");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");
		    echo " * ". $lang['EXP_MYSQL_1']." \n";
		    echo " * ". $lang['EXP_MYSQL_2']." \n";
		    echo " * ". $lang['EXP_MYSQL_3']." \n";
		    echo " *\n";
		    echo " * ". $lang['EXP_MYSQL_4']." \n";
		    echo " *\n";
		    echo " * ". $lang['EXP_MYSQL_5']." \n";
		    echo " *\n";
		    echo " * ". $lang['EXP_MYSQL_6']." \n";
		    echo " * ". $lang['EXP_MYSQL_7']." \n";
		    echo " * ". $lang['EXP_MYSQL_8']." \n";
		    echo " * ". $lang['EXP_MYSQL_9']." ". date("l F j Y, H:i:s\n");
		    echo " * ". $lang['EXP_MYSQL_10']." \n";
		    echo " * ". $lang['EXP_MYSQL_11']." \n";
		    echo " * ". $lang['TAB']." ".VERSION_NO ." \n";
		    echo " *\n";
    // The following block of code must be automated.
		    echo("\n\n");
			echo "DROP TABLE IF EXISTS " . TABLE_ADDITIONALDATA . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_ADDRESS . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_CONTACT . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_EMAIL . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_GROUPLIST . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_GROUPS . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_MESSAGING . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_OPTIONS . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_OTHERPHONE . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_WEBSITES . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_USERS . ";\n";
			echo "DROP TABLE IF EXISTS " . TABLE_SCRATCHPAD . ";\n";
			echo "CREATE TABLE contact_address (refid int(11) NOT NULL auto_increment, id int(11) NOT NULL default '0', line1 varchar(100) default NULL, city varchar(50) default NULL, state varchar(50) default NULL, zip varchar(20) default NULL, country char(3) default NULL, phone1 varchar(20) default NULL, phone2 varchar(20) default NULL, PRIMARY KEY  (`refid`) ) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_contact (id int(11) NOT NULL auto_increment, fullname varchar(100) default NULL, icnum varchar(15) default NULL, title varchar(100) default NULL, primaryAddress int(11) default NULL, pictureURL varchar(255) default 'nopicture.gif', lastUpdate datetime default NULL, hidden int(1) NOT NULL default '0', whoAdded varchar(15) default NULL, delflag int(11) NOT NULL default '0', PRIMARY KEY  (`id`)) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_email (id int(11) NOT NULL default '0', email varchar(100) default NULL) TYPE=MyISAM;";
			echo "CREATE TABLE contact_grouplist (groupid int(11) NOT NULL default '0', groupname varchar(60) default NULL, description text, PRIMARY KEY  (groupid)) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_groups (id int(11) NOT NULL default '0', groupid int(11) NOT NULL default '0') TYPE=MyISAM; \n";
			echo "CREATE TABLE " . TABLE_OPTIONS . " (bdayInterval INT(3) DEFAULT '21' NOT NULL, bdayDisplay INT(1) DEFAULT '1' NOT NULL, displayAsPopup INT(1) DEFAULT '0' NOT NULL, useMailScript INT(1) DEFAULT '1' NOT NULL, picAlwaysDisplay INT(1) DEFAULT '0' NOT NULL, picWidth INT(1) DEFAULT '140' NOT NULL, picHeight INT(1) DEFAULT '140' NOT NULL, picDupeMode INT(1) DEFAULT '1' NOT NULL, picAllowUpload INT(1) DEFAULT '1' NOT NULL, modifyTime VARCHAR(3) DEFAULT '0' NOT NULL, msgLogin TEXT NULL, msgWelcome VARCHAR(255) NULL, countryDefault CHAR(3) DEFAULT '0' NULL, allowUserReg INT(1) DEFAULT '0' NOT NULL, eMailAdmin int(1) NOT NULL default '0', requireLogin INT(1) DEFAULT '1' NOT NULL, language VARCHAR(25) NOT NULL, defaultLetter char(2) default NULL, limitEntries smallint(3) NOT NULL default '0') TYPE=MyISAM;\n";
			echo "CREATE TABLE " . TABLE_USERS . " (id INT(2) NOT NULL AUTO_INCREMENT, username VARCHAR(15) NOT NULL, usertype ENUM('admin','user','guest') NOT NULL DEFAULT 'user', password VARCHAR(32) NOT NULL DEFAULT '', email VARCHAR(50) NOT NULL, confirm_hash VARCHAR(50) NOT NULL, is_confirmed TINYINT(1) DEFAULT '0' NOT NULL, bdayInterval int(3) default NULL, bdayDisplay int(1) default NULL, displayAsPopup int(1) default NULL, useMailScript int(1) default NULL, language varchar(25) default NULL, defaultLetter char(2) default NULL, limitEntries smallint(3) NOT NULL default '0', PRIMARY KEY (id), UNIQUE KEY username (username)) TYPE=MyISAM;\n";
			echo "CREATE TABLE contact_confidential (con_refid int(11) NOT NULL auto_increment, con_id int(11) default '0', con_line1 text, con_city varchar(100) default NULL, con_state varchar(100) default NULL, con_zip varchar(100) default NULL, con_country varchar(100) default NULL, con_phone1 varchar(100) default NULL, con_phone2 varchar(100) default NULL, con_resume1 varchar(200) default NULL, con_resume2 varchar(200) default NULL, PRIMARY KEY  (con_refid)) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_contribution (refid int(11) NOT NULL auto_increment, committee text NOT NULL, position varchar(200) NOT NULL default '', year varchar(100) NOT NULL default '', id int(11) NOT NULL default '0', PRIMARY KEY  (refid)) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_expertise (area_id int(11) NOT NULL default '0', area_name varchar(200) default NULL, area_desc text, PRIMARY KEY  (area_id)) TYPE=MyISAM; \n";
			echo "CREATE TABLE contact_expertlink (id int(11) NOT NULL default '0', area_id int(11) NOT NULL default '0') TYPE=MyISAM; \n";

			// GET AND OUTPUT ALL THE DATA
			$tables = array(TABLE_ADDRESS, TABLE_CONTACT, TABLE_EMAIL, TABLE_GROUPLIST, TABLE_GROUPS, TABLE_OPTIONS, TABLE_USERS, TABLE_CONFIDENTIAL, TABLE_CONTRIBUTION, TABLE_EXPERTISE, TABLE_EXPERTLINK);
			while ($a = each($tables)) 
			{
				createInsertQuery($a[1]);
			}
			break;

/********************************************************************************
 ** EUDORA NICKNAMES FORMAT
 **
 ********************************************************************************/
		case "eudora":

			// Retrieve data associated with given ID
			$nnListQuery = "SELECT contact.id, fullname, email FROM " . TABLE_CONTACT . " 
							AS contact, " . TABLE_EMAIL . " AS email WHERE contact.id=email.id AND
							contact.delflag != 1 AND email.email != '' ORDER BY contact.id AND delflag != 1 AND hidflag != 1";


		    $r_contact = mysql_query($nnListQuery, $db_link)
				or die(reportSQLError($nnListQuery));

			// OUTPUT
		    header("Content-type: text/plain");
			header("Content-disposition: attachment; filename=NNdbase.txt");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");

		    while ($tbl_contact = mysql_fetch_array($r_contact)) {
		        echo("\n");
		        echo('alias "' . 
		              $tbl_contact['fullname'] . '" ' .
		              $tbl_contact['fullname'] . ' <' . 
		              $tbl_contact['email'] . '>');
		    }
		
			// END
			break;

/********************************************************************************
 ** COMMA-SEPARATED VALUES (CSV) FORMAT
 **
 ** thanks to sineware
 ********************************************************************************/
		case "csv":

			// QUERY
			$csvQuery = "SELECT contact.id, 
							fullname, 
		    		    	email.email, 
							address.line1, 
							address.city, 
							address.state, 
							address.zip, 
							address.phone1, 
							address.phone2
				     		FROM ". TABLE_CONTACT ." AS contact
							LEFT JOIN ". TABLE_EMAIL ." AS email ON contact.id = email.id
			        		LEFT JOIN ". TABLE_ADDRESS ." AS address ON address.id = contact.id
							WHERE delflag != 1 AND hidflag != 1
							AND address.refid = contact.primaryAddress";
									
		    $r_contact = mysql_query($csvQuery, $db_link) or die(reportSQLError($csvQuery));

			// OUTPUT
			header("Content-Type: text/comma-separated-values");
			header("Content-disposition: attachment; filename=Asmic.csv");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");

		    echo("First name, Email, Address, City, State, Zip, Phone1, Phone2 \n");
			while ($tbl_contact = mysql_fetch_array($r_contact)) 
			{
				// Most  variables are checked for the comma (,) character, which will be
				// removed if found. This is to prevent these fields from breaking the CSV format.
				
				$tempfullname = $tbl_contact['fullname'];
				$tempemail = $tbl_contact['email'];
				$templine1 = str_replace("\n", " ", $tbl_contact['line1']);
				$tempcity = $tbl_contact['city'];
				$tempstate = $tbl_contact['state'];
				$tempzip = $tbl_contact['zip'];
				$tempphone1 = $tbl_contact['phone1'];
				$tempphone2 = $tbl_contact['phone2'];
	
				$comma = ",";
				$doublequotes = '"';
				$singlequotes = "'";
				$fullname = str_replace($comma, " ", $tempfullname);
				$email = $tempemail;
				$line1 = str_replace($comma, " ", $templine1);
				$city = str_replace($comma, " ", $tempcity);
				$state = str_replace($comma, " ", $tempstate);
				$zip = str_replace($comma, " ", $tempzip);
				$phone1 = str_replace($comma, " ", $tempphone1);
				$phone2 = str_replace($comma, " ", $tempphone2);
				
				settype($tempzip, "string");
				
				echo ($tempfullname . "," . $tempemail . "," .$doublequotes.$templine1 .$doublequotes. "," . $tempcity . "," . $tempstate . "," . $tempzip . "," . $tempphone1 . "," . $tempphone2  . "\n");
		    }

			break;

/********************************************************************************
 ** TEXT FORMAT
 **
 ** (thanks to David Léonard) -- Beta, but working. -- broken pending existence of acessBD.php
 ********************************************************************************/
		case "text":
		
		    $query ="SELECT 
					  `address_contact`.`id`,
					  `address_contact`.`fullname`,
					  `address_contact`.`primaryAddress`,
					  `address_contact`.`pictureURL`,
					  `address_contact`.`lastUpdate`,
					  `address_contact`.`hidden`,
					  `address_contact`.`whoAdded`,
					  `address_address`.`line1`,
					  `address_address`.`city`,
					  `address_address`.`state`,
					  `address_address`.`zip`,
					  `address_address`.`country`,
					  `address_address`.`phone1`,
					  `address_address`.`phone2`
					FROM
					  `address_contact`
					  INNER JOIN `address_address` ON (`address_contact`.`id` = `address_address`.`id`)";
							
		    $data 	= new accesBDlecture ($query,"","");
		    $query	= "SELECT * FROM address_grouplist WHERE 1";
		    $entete = new accesBDlecture($query,"","");
		    
			// OUTPUT
		    header("Content-type: text/plain");
			header("Content-disposition: attachment; filename=tab.txt");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");

		    
		    //affichage des entetes communs
		    //echo "NUMERO\tPRENOM\tNOM\tTITRE\tANNIVERSAIRE\tMÀJ LE\tPROPRIETAIRE\tTYPE ADRESSE\tADRESSE1\tADRESSE2\tVILLE\tETAT\tNPA\tPAYS\tTEL1\tTEL2\t";
		    echo "NUMERO\tNAME\tMÀJ LE\tPROPRIETAIRE\tTYPE ADRESSE\tADRESSE1\tVILLE\tETAT\tNPA\tPAYS\tTEL1\tTEL2\t";
		    //affichage des entetes correspondant aux noms des groupes
		    foreach ($entete->row as $courant) 
			{
		    	if ($courant == NULL) break;
		    	if ($courant->groupid <3)
		    		{continue;}
		    	else
		    		{echo"$courant->groupname\t";}
		    }
		    echo"\n";
		    
		    //remplissage des données suivant les entetes
		    foreach ($data->row as $donnee) 
			{
		    	if ($donnee == NULL) break;
		    	//sélection du nom du pays
		    	$query 				= "SELECT countryname FROM address_country WHERE id = ".$donnee->country." ";
		    	$pays 				= new accesBDlecture($query,"","");
		    	$paysCourant 	= $pays->row[0]->countryname;
		    	
		    	//affichage des données communes
		    	//echo "$donnee->id\t$donnee->firstname\t$donnee->lastname\t$donnee->nickname\t$donnee->birthday\t$donnee->lastUpdate\t$donnee->whoAdded\t$donnee->type\t$donnee->line1\t$donnee->line2\t$donnee->city\t$donnee->state\t$donnee->zip\t$paysCourant\t$donnee->phone1\t$donnee->phone2\t";
		    	echo "$donnee->id\t$donnee->fullname\t$donnee->lastUpdate\t$donnee->whoAdded\t$donnee->line1\t$donnee->city\t$donnee->state\t$donnee->zip\t$paysCourant\t$donnee->phone1\t$donnee->phone2\t";
		    	//sélection des des groupes dont fait partie l'adresse courante
		    	$query = "SELECT * FROM address_groups WHERE id =".$donnee->id." ";
		    	$groupe = new accesBDlecture($query,"","");
		    	$query	= "SELECT * FROM address_grouplist WHERE 1 ORDER BY 1";
		    	$entete = new accesBDlecture($query,"","");
		    	foreach ($entete->row as $courant) {
		    			if ($courant == NULL) break;
		    			if ($courant->groupid <3)
		    				{continue;}
		    			else
		    				{
		    					$valide = "NON\t";
		    				foreach ($groupe->row as $groupeCourant) {
		    					if ($groupeCourant == NULL)break;
		    					//comparaison avec les groupes actuels
		    					if ($courant->groupid == $groupeCourant->groupid) $valide = "OUI\t";
		    				}
		    				echo $valide;
		    		}
		    	}
		    	echo "\n";
		    	unset ($query,$pays,$paysCourant);

		    }

			// END
			break;

/********************************************************************************
 ** XML FORMAT
 **
 ** XML quick format
 ** Please use export.xsl for formatting output file - NOT YET!
 ** thanks to "mutato" <radio@frequenze.it>!
 ********************************************************************************/
		case "xml":

			// QUERY
			$xmlQuery = "SELECT * FROM ". TABLE_CONTACT . " WHERE delflag != 1 AND hidflag != 1";
			$r_contact = mysql_query($xmlQuery, $db_link);

			// OUTPUT
			header("Content-type: text/xml");
			header("Content-disposition: attachment; filename=Asmic.xml");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");

			echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n\n";
			echo "<rubrica>\n\n";

			while ($tbl_contact = mysql_fetch_array($r_contact)) 
			{

			# short id
			$XID = $tbl_contact['id'];

			echo "<CONTACT id=\"".$XID."\" update=\"".$tbl_contact['lastUpdate']."\">\n";

			# personal data from TABLE_CONTACT
			echo "<PERSONALDATA>\n";
			echo "<fullname>".$tbl_contact['fullname']."</fullname>\n";
			echo "</PERSONALDATA>\n";

			# below this line you can move
			# up or down section data

			# ********************
			# TABLE_EMAIL
			# ********************
			echo "<EMAIL>\n";
			$xmlMail = "SELECT * FROM ". TABLE_EMAIL . " WHERE id=$XID";
			$r_mail = mysql_query($xmlMail, $db_link);

			while ($tbl_mail = mysql_fetch_array($r_mail)) {
				echo "<mail type=\"".$tbl_mail['type']."\">".$tbl_mail['email']."</mail>\n";
			} 

			echo "</EMAIL>\n";
			# ********************
			# /END TABLE_EMAIL 
			# ********************

			# ********************
			# TABLE_ADDRESS 
			# ********************
			echo "<ADDRESS>\n";

			$xmlAddr = "SELECT * FROM ". TABLE_ADDRESS . " WHERE id=$XID";
			$r_addr = mysql_query($xmlAddr, $db_link);

			while ($tbl_addr = mysql_fetch_array($r_addr)) 
			{

			//echo "<address type=\"".$tbl_addr['type']."\">\n";
			echo "<line1>".$tbl_addr['line1']."</line1>\n";
			//echo "<line2>".$tbl_addr['line2']."</line2>\n";
			echo "<city>".$tbl_addr['city']."</city>\n";
			echo "<state>".$tbl_addr['state']."</state>\n";
			echo "<zip>".$tbl_addr['zip']."</zip>\n";

			# TABLE_COUNTRY
			$xmlCountry = $tbl_addr['country'];

			echo "<country>".$country[$xmlCountry]."</country>\n";
			echo "<phone1>".$tbl_addr['phone1']."</phone1>\n";
			echo "<phone2>".$tbl_addr['phone2']."</phone2>\n";
			//echo "</address>\n";

			} 

			echo "</ADDRESS>\n";
			# ********************
			# /END TABLE_ADDRESS 
			# ********************


			# ********************
			# GROUPS SUBSCRIPTIONS
			# ********************
			echo "<GROUPS>\n";
			$xmlGroups = "SELECT * FROM ". TABLE_GROUPS . " WHERE id=$XID";
			$r_groups = mysql_query($xmlGroups, $db_link);

			while ($tbl_groups = mysql_fetch_array($r_groups)) {

			# groups name
			$xmlGN = "SELECT * FROM ". TABLE_GROUPLIST . " WHERE groupid=".$tbl_groups['groupid']."";
			$r_gn = mysql_query($xmlGN, $db_link);
			$tbl_gn = mysql_fetch_array($r_gn);


			echo "<group id=\"".$tbl_gn['groupid']."\" name=\"".$tbl_gn['groupname']."\"/>\n";

			} 

			echo "</GROUPS>\n";
			# ***********************
			# /END GROUPS SUBSCRIPTION
			# ***********************

			#### do not move ########
			echo "</CONTACT>\n\n";
			} 
			### close xmlQuery ######


			echo "</rubrica>";

			// END
			break;


/********************************************************************************
 ** GMAIL-IMPORTABLE CSV FORMAT
 **
 ********************************************************************************/
		case "gmail":

			// QUERY
			$gmailQuery = "SELECT fullname, email FROM ". TABLE_CONTACT ." 
							AS contact LEFT JOIN ". TABLE_EMAIL ." AS email ON 
							contact.id=email.id WHERE email.email != '' AND contact.delflag != 1 AND contact.hidflag != 1";
		    
			$r_contact = mysql_query($gmailQuery, $db_link) or die(reportSQLError($gmailQuery));

			// OUTPUT
			header("Content-Type: text/comma-separated-values");
			header("Content-disposition: attachment; filename=gmail.csv");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Expires: 0");

		    echo("Name,Email Address\n");
			
		    while ($tbl_contact = mysql_fetch_array($r_contact)) 
			{
				echo(str_replace(",", "",$tbl_contact['fullname']));
				echo("," . $tbl_contact['email'] . "\n");
		    }

			// END
			break;
			
		case "vcard":  //from wilco on forum http://www.corvalis.net/phpBB2/viewtopic.php?t=294
		
			$vCardQuery = "SELECT id, fullname, pictureURL FROM ". TABLE_CONTACT." WHERE delflag != 1";
			

			$r_contact = mysql_query($vCardQuery, $db_link) or die(reportSQLError($vCardQuery));
				
			$mobile_prefix = '06';
			$picture_prefix = 'http://asmic.devx/contact/mugshots/';
			
			
			//include('vcard.php');
			while($r = mysql_fetch_array($r_contact)) 
			{  
				$output .= "BEGIN:VCARD\nVERSION:3.0\n";
				$output .= 'FN:' . $r['fullname'] . "\n";
				$output .= 'N:' . $r['fullname'] . "\n";
				//if($r['nickname']) $output .= 'NICKNAME:' . $r['nickname'] . "\n";
				if($r['pictureURL']) $output .= 'PHOTO;VALUE=uri:' . $picture_prefix . $r['pictureURL'] . "\n";
				//if($r['birthday'] != '0000-00-00') $output .= 'BDAY:' . $r['birthday'] . "\n";
				
				$i='primary';
				$adrq = 'SELECT line1, city, state, phone1, phone2, zip FROM ' . TABLE_ADDRESS . ' WHERE id=' . $r['id'];
				$adrq = mysql_query($adrq);
				while($adr = mysql_fetch_array($adrq)) 
				{
					$output .= 'ADR;TYPE=dom,home,postal';
					if($i == 'primary') 
					{
						$output .= ',pref';
					}
					
					$output .= ':;;' . $adr['line1'] . ';' . $adr['city'] . ';' . $adr['state'] . ';' . $adr['zip'] . "\n";
					
					
					if($adr['phone1']) 
					{
						$output .= 'TEL;TYPE=';
					
						if(eregi("^$mobile_prefix",$adr['phone1'])) 
						{
							$output .= 'CELL,VOICE,MSG';
							if($i == 'primary') $output .= ',PREF';
						}
						else 
						{
							$output .= 'HOME,VOICE';
							if($i == 'primary') $output .= ',PREF';
						}
					
						$output .= ':' . $adr['phone1'] . "\n";
					}
					
					if($adr['phone2']) 
					{
						$output .= 'TEL;TYPE=';
						if(eregi("^$mobile_prefix",$adr['phone2'])) $output .= 'CELL,VOICE,MSG';
						else $output .= 'HOME,VOICE';
					
						$output .= ':' . $adr['phone2'] . "\n";
					}
					
					$i = 'not_primary';
				}
								
				$emailq = 'SELECT email FROM ' . TABLE_EMAIL . ' WHERE id=' . $r['id'];
				$emailq = mysql_query($emailq);
				$i = 'primary';
				while($m = mysql_fetch_array($emailq)) 
				{
					$output .= 'EMAIL;TYPE=internet,home';
					if($i == 'primary') $output .= ',PRIM';
					$output .= ':' . $m['email'] . "\n";
					$i = 'not_primary';
				}				
				
				$output .= "END:VCARD\n";
				$output .= "\n";				
				
				
			}
			
			// for debugging
			//echo nl2br($output);
			
			header("Content-Disposition: attachment; filename=contact.vcf");
			header("Content-Length: ".strlen($output));
			header("Connection: close");
			header("Content-Type: text/x-vCard; name=contact.vcf");
			
			echo $output;
	
		
		break;

/******************************************************************************
 ** EXPORT MAIN MENU
 ********************************************************************************/

		// ** EXPORT MENU
		default:

include_once($app_absolute_path."includes/template_header.php");
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr> 
<td width="4%" rowspan="13"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="24" height="8"></td>
<td>&nbsp;</td>
<td width="1%" rowspan="13"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="12" height="8"></td>
</tr>
<tr> 
<td class="module_title">ASM Contact</td>
</tr>
<tr> 
<td class="breadcrumbs"><? require_once('breadcrumb.php'); ?></td>
</tr>
<tr> 
<td><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="2" height="13" border="0"></td>
</tr>
<tr> 
<td>
<? require_once('navigation.php'); ?>
</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td> 
<? require_once('searchform.php'); ?>
</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td class="m5_td_header"> <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="m5_td_header">
              <tr> 
                <td><strong>Export Contacts</strong></td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td valign="top" class="m5_td_content">This will export the ASM contact 
            database to a file. If a &quot;Save As&quot; dialog box does not appear, 
            right click the link instead and choose &quot;Save Target As...&quot;</td>
        </tr>
        <!--<tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=eudora">Eudora Nicknames file</a> 
                (copy the output to /Eudora/NNdbase.txt)</li>
            </td>
        </tr>-->
        <!--<tr> 
          <td class="m5_td_content">
     		<li><a href="export.php?format=mysql">mySQL Dump File</a> (backup 
                purposes)</li>
		</td>
        </tr>-->
        <tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=csv">CSV File</a></li>
            </td>
        </tr>
        <!--<tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=text">Plain text</a> (with separator) 
                (broken)</li>
            </td>
        </tr>-->
        <tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=xml">XML</a></li>
            </td>
        </tr>
        <tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=gmail">Gmail</a> (Importable contact 
                list)</li>
            </td>
        </tr>
        <!--<tr> 
          <td class="m5_td_content">
              <li><a href="export.php?format=vcard">vCard</a> (warning: downloads 
                to your computer and installs in your address book with no further 
                prompting!)</li>
            </td>
        </tr>-->
      </table></td>
              </tr>
			  <tr>
			  <td>&nbsp;
			  </td>
			  </tr>
            </table>
<? 
			include_once($app_absolute_path."includes/template_footer.php");
			break;
	}
?>
