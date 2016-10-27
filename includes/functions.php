<?php
function isAllowed($codes, $permissions){
	if (empty($permissions))
		return false;
	elseif (is_array($permissions)){
		$isAllowed = false;
		foreach ($codes as $code){
			if (in_array($code, $permissions))
				$isAllowed = true;
		}
		return $isAllowed;
	}
	else
		return false;
}

function requestString($obj, $default=''){
	if (empty($obj)) return $default;
	else return $obj;
}

function requestNumber($obj, $default=0){
	if (empty($obj)) return $default;
	else return $obj;
}

function generateLink($text, $url=''){
	if (empty($url))
		return $text;
	else {
		return "<a class=\"hyperlink\" href=\"$url\">$text</a>";
	}
}

/* table functions */
/*	function: beginTable
	purpose: print table opening tag */
function beginTable($border=0, $cellpadding=3, $cellspacing=1, $bgcolor='#000000'){
	echo("<table border=\"$border\" cellpadding=\"$cellpadding\" cellspacing=\"$cellspacing\" bgcolor=\"$bgcolor\">");
}

/* 	function: endTable
	purpose: print table closing tag */
function endTable(){
	echo("</table>");
}

/*	function: printTableHeader
	purpose: print a table header*/
function printTableHeader($columns, $bgcolor='#CCCCCC'){
	echo("<tr bgcolor=\"$bgcolor\">");
	foreach($columns as $column){
		echo("<td>$column</td>");
	}
	echo("</tr>");
}

/*	function : printTableRow
	purpose: print a table row */
function printTableRow($row, $bgcolor='#FFFFFF'){
	echo("<tr bgcolor=\"$bgcolor\">");
	foreach($row as $column){
		echo("<td>$column</td>");
	}
	echo("</tr>");
}

function DateConvert($old_date, $layout) 
{ 
//Remove non-numeric characters that might exist (e.g. hyphens and colons) 
$old_date = ereg_replace('[^0-9]', '', $old_date); 

//Extract the different elements that make up the date and time 
$_year = substr($old_date,0,4); 
$_month = substr($old_date,4,2); 
$_day = substr($old_date,6,2); 
$_hour = substr($old_date,8,2); 
$_minute = substr($old_date,10,2); 
$_second = substr($old_date,12,2); 

//Combine the date function with mktime to produce a user-friendly date & time 
$new_date = date($layout, mktime($_hour, $_minute, $_second, $_month, $_day, $_year)); 
return $new_date; 
} 


function dateFormat($input_date, $input_format, $output_format) { 
   preg_match("/^([\w]*)/i", $input_date, $regs); 
   $sep = substr($input_date, strlen($regs[0]), 1); 
   $label = explode($sep, $input_format); 
   $value = explode($sep, $input_date); 
   $array_date = array_combine($label, $value); 
   if (in_array('Y', $label)) { 
       $year = $array_date['Y']; 
   } elseif (in_array('y', $label)) { 
       $year = $year = $array_date['y']; 
   } else { 
       return false; 
   } 

   $output_date = date($output_format, mktime(0,0,0,$array_date['m'], $array_date['d'], $year)); 
   return $output_date; 
} 
?>