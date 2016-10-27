<?
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


// ** START SESSION **
	//session_start();
	
// ** OPEN CONNECTION TO THE DATABASE **
    $db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);
		
	$vcard = $_GET['vcard'];
	
	if ($vcard == 'vcard')
	{
			
			$id = $_GET['id'];
				
			$vCardQuery = "SELECT id, fullname, pictureURL FROM contact_contact WHERE delflag != 1 AND id = $id";
			

			$r_contact = mysql_query($vCardQuery, $db_link) or die(reportSQLError($vCardQuery));
							
			$mobile_prefix = '06';
			$picture_prefix = 'http://asmic.gotdns.com/contact/mugshots/';
			
			//include('vcard.php');
			while($r = mysql_fetch_array($r_contact)) 
			{  
				$fullname = $r['fullname'];
				$tempfullname = str_replace(" ", "_", $fullname);
				$output .= "BEGIN:VCARD\nVERSION:3.0\n";
				$output .= 'FN:' . $fullname . "\n";
				$output .= 'N:' . $fullname . "\n";
				if($r['pictureURL']) 
				$output .= 'PHOTO;VALUE=uri:' . $picture_prefix . $r['pictureURL'] . "\n";
				
				$i = 'primary';
				$adrq = 'SELECT line1, city, state, phone1, phone2, phone3, fax1, fax2, zip FROM contact_address WHERE id=' . $r['id'];
				$adrq = mysql_query($adrq);
				while($adr = mysql_fetch_array($adrq)) 
				{
					//$output .= 'ADR;TYPE=dom,home,postal';
					//$output .= 'ADR;TYPE=home';
					if($i == 'primary') 
					{	
						$output .= 'ADR;TYPE=dom';
						$output .= ',pref';
					}
					else
					{
						$output .= 'ADR;TYPE=home';
					}
					$line1 = str_replace("\n", " ", $adr['line1']);
					//$output .= ':;;' . $adr['line1'] . ';' . $adr['city'] . ';' . $adr['state'] . ';' . $adr['zip'] . "\n";
					$output .= ':;;' . $line1 . ';' . $adr['city'] . ';' . $adr['state'] . ';' . $adr['zip'] . "\n";
										
					if($adr['phone1']) 
					{
						$output .= 'TEL;TYPE=';
					
						if(eregi("^$mobile_prefix",$adr['phone1'])) 
						{
							//$output .= 'CELL,VOICE,MSG';
							$output .= 'CELL';
							if($i == 'primary') 
							$output .= ',PREF';
						}
						else 
						{
							//$output .= 'HOME,VOICE';
							$output .= 'CELL';
							//if($i == 'primary') 
							//$output .= ',PREF';
						}
					
						$output .= ':' . $adr['phone1'] . "\n";
					}
					
					if($adr['phone2']) 
					{
						$output .= 'TEL;TYPE=';
						if(eregi("^$mobile_prefix",$adr['phone2']))
						{ 
							//$output .= 'CELL,VOICE,MSG';
							$output .= 'HOME';
						}						
						else
						{ 
						 	$output .= 'HOME,VOICE';
						}
					
						$output .= ':' . $adr['phone2'] . "\n";
					}
					
					if($adr['phone3']) 
					{
						$output .= 'TEL;TYPE=';
						if(eregi("^$mobile_prefix",$adr['phone3']))
						{ 
							//$output .= 'CELL,VOICE,MSG';
							$output .= 'WORK';
						}						
						else
						{ 
						 	$output .= 'WORK,VOICE';
						}
					
						$output .= ':' . $adr['phone3'] . "\n";
					}
					
					if($adr['fax1']) 
					{
						$output .= 'TEL;TYPE=';
						if(eregi("^$mobile_prefix",$adr['fax1']))
						{ 
							//$output .= 'CELL,VOICE,MSG';
							$output .= 'FAX';
						}						
						else
						{ 
						 	$output .= 'WORK,FAX';
						}
					
						$output .= ':' . $adr['fax1'] . "\n";
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
			
			//for debugging
			//echo nl2br($output);
			
			header("Content-Disposition: attachment; filename=$tempfullname.vcf");
			header("Content-Length: ".strlen($output));
			header("Connection: close");
			header("Content-Type: text/x-vCard; name=$tempfullname.vcf");
			
			echo $output;

	}
?>
