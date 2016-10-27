<?php
session_start();

  include("class.php");
  include("errors.php");

  //GET PARAMETERS
  if(isset($_REQUEST["book"])) $book = $_REQUEST["book"];
  else $book = "";
  
  if(isset($_REQUEST["cat"])) $cat = $_REQUEST["cat"];
  else $cat = "";
  
  if(isset($_REQUEST["action"])) $action = $_REQUEST["action"];
  else $action = "";
  
  //CALL OUT TO DELETE CATEGORY
  if ($cat=='delete') {
      $cat = $_REQUEST["catid"];            
	  $libdb = new Modules_sql;
	  
	  $qry ="select * from library_category where id=".$cat."";
	  $libdb->query($qry);
	  $libdb->next_record();
	  $ct_id = $libdb->record[1];
	  $catname = $libdb->record[2];
	  $flg = 'UN';
	  $qry_flag ="update library_books set category_id='".$flg."' where category_id='".$ct_id."';";
	  $libdb->query($qry_flag);
	  
	  $qry_str = "delete from library_category where id='".$cat."';";
	  $libdb->query($qry_str);
	  include_once("../classes/audit_trail.php");
	  $audit_trail = new audit_trail();
	  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Delete Category : ".$catname."");
	  
	  echo'<meta http-equiv="refresh" content="0;URL=category.php?action=cfm_delete&del=0">';
  }
  
	//RETURN BOOK (FOR LIBRARIAN ONLY)
	if($action=='return') {
	  $libdb = new Modules_sql;
	
	  $current_date = date("Y-m-d");
	  $date_return = $_REQUEST["date_return"];
	  $book_code = $_REQUEST["book_code"];
	  $id = $_REQUEST["id"];
	  
	  //SET RETURN DATE IN THE ISSUE TABLE (UPDATE)
	  $date_r = changeDate($date_return);
	  $qry_str = "update library_issue set date_return = '".$date_r."' where accession_no = '".$book_code."' and contact_id= '".$id."' and date_return='0000-00-00';";
	  $libdb->query($qry_str);
	  //UPDATE BOOK STATUS. SET TO AVAILABLE = 'y'
	  $qry_str = "update library_books_unit set book_status = 'y' where accession_no = '".$book_code."';";
	  $libdb->query($qry_str);
	  
	  //UPDATE CARD MEMBER
	  $qry_str = "update library_member set member_cards = member_cards + 1 where contact_id = '".$id."';";
	  $libdb->query($qry_str);
	  
	 include_once("../classes/audit_trail.php");
	 $audit_trail = new audit_trail();
	 $audit_trail->writeLog($_SESSION['usr_username'], "library", "Return Books : ".$book_code."");
	 echo'<meta http-equiv="refresh" content="0;URL=bookreturn.php?action=return">';
	  
	}
	
	//CANCEL RESERVATIONS/ISSUING BOOK (LIBRARIAN)
	if ($action=='delissue') {
	  $libdb = new Modules_sql;
	  
	  $cart = $_REQUEST["cart"];
	  $userid = $_REQUEST["id"];
	  $book = $_REQUEST["book"];
	  $qry_str = "delete from library_issue where issue_id=".$cart."";
	  $libdb->query($qry_str);
	  $qry_str = "update library_books_unit set book_status='y' where accession_no='".$book."'";
	  $libdb->query($qry_str);
	  $qry_str = "update library_member set member_cards=member_cards + 1 where contact_id=".$userid."";
	  $libdb->query($qry_str);
	  
	   include_once("../classes/audit_trail.php");
	   $audit_trail = new audit_trail();
	   $audit_trail->writeLog($_SESSION['usr_username'], "library", "Cancel Issuing Book : ".$book."");
	    echo'<meta http-equiv="refresh" content="0;URL=bookissue.php?action=success&id='.$userid.'">';

	}
	
	//CANCEL RESERVATION (MEMBER - ONLINE PROCESS)
	if ($action=='cancelreserve') {
	  $libdb = new Modules_sql;
	   
	  $cart = $_REQUEST["cart"];
	  $user = $_REQUEST["id"];		 
	  $book = $_REQUEST["book"];
	  $qry_str = "delete from library_issue where issue_id=".$cart."";
	  $libdb->query($qry_str);
	  $qry_str = "update library_books_unit set book_status='y' where accession_no='".$book."'";
	  $libdb->query($qry_str);
	  $qry_str = "update library_member set member_cards=member_cards + 1 where contact_id=".$user."";
	  $libdb->query($qry_str);
	  $qry_str = "select fullname from contact_contact where id=".$user."";
	  $libdb->query($qry_str);
	  $libdb->next_record();
	  $name = $libdb->record[0];																														
	  
	   include_once("../classes/audit_trail.php");
	   $audit_trail = new audit_trail();
	   $audit_trail->writeLog($_SESSION['usr_username'], "library", "Cancel Reservation Book by ".$name." for book ".$book."");
	    echo'<meta http-equiv="refresh" content="0;URL=index.php">';

	}
	
	//CANCEL RESERVATIONS BOOK WHICH HAVEN'T PROOF BY MEMBER (MORE THAN ONE DAY) LIBRARIAN PROCESS
	if ($action=='delreserve') {
	  $libdb = new Modules_sql;
	  
	  $cart = $_REQUEST["cart"];
	  $userid = $_REQUEST["id"];
	  $book = $_REQUEST["book"];
	  $qry_str = "delete from library_issue where issue_id=".$cart."";
	  $libdb->query($qry_str);
	  $qry_str = "update library_books_unit set book_status='y' where accession_no='".$book."'";
	  $libdb->query($qry_str);
	  $qry_str = "update library_member set member_cards=member_cards + 1 where contact_id=".$userid."";
	  $libdb->query($qry_str);
	  
	   include_once("../classes/audit_trail.php");
	   $audit_trail = new audit_trail();
	   $audit_trail->writeLog($_SESSION['usr_username'], "library", "Cancel Reservation Book : ".$book."");
	   echo'<meta http-equiv="refresh" content="0;URL=bookreserve.php?action=reserve_confirm&res=1&id='.$userid.'">';	
	}
	
	//SETTING FOR LOAN DURATION
	if ($action=='settings') {
	  $libdb = new Modules_sql;
	  $dt_added = date("Y-m-d H:i:s");
	  $issue_period = $_REQUEST["issue_period"];
	  $fine = $_REQUEST["fine"];
	  $cards_issued = $_REQUEST["cards_issued"];
	  
	  $qry_str = "update library_settings set issue_period='".$issue_period."', cards_issued = ". $cards_issued . ";";
	  $libdb->query($qry_str);
	  if ($libdb->affected_rows() < 1) {
		/* this seems to be a fresh database w/o any records. lets insert one ... */
		$qry_str = "insert into library_settings(issue_period, cards_issued, date_added) values ('".$issue_period."','".$cards_issued."','".$dt_added."');";
		$libdb->query($qry_str);
		}
	   include_once("../classes/audit_trail.php");
	   $audit_trail = new audit_trail();
	   $audit_trail->writeLog($_SESSION['usr_username'], "library", "Change Setting");
	   echo'<meta http-equiv="refresh" content="0;URL=settings.php?action=done">';
	}

?>