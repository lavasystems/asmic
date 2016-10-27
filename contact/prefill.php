<?	
include_once($app_absolute_path."includes/template_header.php");
?>
<br><br><br><br><br><br>		
<table width="300" align="center" border="1" cellpadding="1" cellspacing="0" bordercolor="#FF0000">
<tr>
	<td class="ar11_content">
	<div align="center"><? echo $errormessage; ?></div>
<?
// ** CHECKS THE $ID, IF $ID EXIST, THAT MEANS THE USER IS EDITING, 
// IF $ID NOT EXIST, IT MEANS NEW USER IS BEING CREATED ** 
if (!empty($id))
{
	//$action = "edit.php?id=$id";
	$action = "edit.php?id=$id&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
}
else
{
	//$action = "edit.php?mode=new";
	$action = "edit.php?mode=new&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
}
?>
	<form method="post" action="<? echo $action; ?>" enctype="multipart/form-data">
	<table align="center">
	<tr>
		<td>
		<input name="fullname" type="hidden" value="<? echo $tempname; ?>">
		<input name="title" type="hidden" value="<? echo $temptitle; ?>">
		<input name="icnum" type="hidden" value="<? echo $tempic; ?>">
		<input name="email" type="hidden" value="<? echo $tempemail; ?>">
		<input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>">
		<input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>">
		<input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>">
		<input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>">
		<input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>">
		<input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>">
		<input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>">
		
		<input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>">
		<input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>">
		<input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>">
		<input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>">
		<input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>">
		<input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>">
		<input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>">

		<input name="con_line1" type="hidden" value="<? echo $confaddress; ?>">
		<input name="con_city" type="hidden" value="<? echo $confcity; ?>">
		<input name="con_state" type="hidden" value="<? echo $confstate; ?>">
		<input name="con_zip" type="hidden" value="<? echo $confzip; ?>">
		<input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>">
		<input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>">
		<input name="address_country" type="hidden" value="<? echo $confcountry; ?>">
		<?
		$j = 0;
		while ($j < $i)
		{
			//echo $areaid[$i]."<br>";
			echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
			$j++;
		}	
		?>
		<input type="hidden" name="selectcat" value="<? echo $categoryid; ?>">
		
		<input type="hidden" name="committee" value="<? echo $contribution; ?>">
		<input type="hidden" name="position" value="<? echo $contribution_pos; ?>">
		<input type="hidden" name="year" value="<? echo $contribution_ye; ?>">
		
		<input type="hidden" name="membercheck" value="<? echo $membercheck; ?>">
		<input type="image" src="../images/m5/m5_btn_back.gif">
		<input type="hidden" name="Submit" value="Submit">
		</td>
		</tr>
		</table>
		</form>
		</td>
		</tr>
		</table>
	
<?
include_once($app_absolute_path."includes/template_footer.php");
exit();
?>	
