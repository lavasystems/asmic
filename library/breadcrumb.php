<?php
	$permissions = $_SESSION['permissions'];
	$main = $_SERVER['PHP_SELF'];
	$pg = $_SERVER['QUERY_STRING'];
	
	if (is_array($permissions) && in_array(501, $permissions)){
		if($main != '/library/index.php') {
		echo "<a href=\"index.php\">Main</a> &gt;";
		
		if (empty($pg) && preg_match('/category/i', $main)) {
			echo "Category";
		}
		if (preg_match('/st/i', $pg) && preg_match('/category/i', $main)) {
			echo "Category";
		}
		if(!empty($pg) && preg_match('/category/i', $main)) {
			if(preg_match('/new/i', $pg)) {
				echo "<a href=\"category.php\">Category</a> &gt; New Category";
			}
			if(preg_match('/add/i', $pg)) {
				echo "<a href=\"category.php\">Category</a> &gt; New Category";
			}
			if(preg_match('/idelete&kat/i', $pg)) {
				echo "<a href=\"category.php\">Category</a> &gt; Delete Category";
			}
			if(preg_match('/cfm_delete/i', $pg)) {
				echo "<a href=\"category.php\">Category</a> &gt; Re-assign Category";
			}
			if($main == '/library/category_details.php' && preg_match('/kat/i',$pg)) {
				if(!empty($_REQUEST['kat'])) {
				$libdb_crumb = new Modules_sql;
				$qry = "select * from library_category where id=".$_REQUEST['kat']."";
				$libdb_crumb->query($qry);
				$libdb_crumb->next_record();
				
				}
			echo "&nbsp;<a href=\"category.php\">Category</a> &gt; ".$libdb_crumb->record[2]."";
			}
			if(preg_match('/update/i', $pg) && preg_match('/kat/i',$pg)) {
				if(!empty($_REQUEST['kat'])) {
				$libdb_crumb = new Modules_sql;
				$qry = "select * from library_category where id=".$_REQUEST['kat']."";
				$libdb_crumb->query($qry);
				$libdb_crumb->next_record();
				
				}
			echo "&nbsp;<a href=\"category.php\">Category</a> &gt; <a href=\"category_details.php?kat=".$libdb_crumb->record[0]."\">".$libdb_crumb->record[2]."</a> &gt; Update Category";
			}	
		}
		if(preg_match('/bookdetails/i', $main)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select b.id, a.category_id, a.book_title, a.book_isbn, b.category_id, b.category_name";
			$qry .=" from library_books a, library_category b where a.book_recordid='".$_REQUEST['id']."' and a.category_id=b.category_id";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"category_details.php?kat=".$libdb_crumb->record[0]."\">".$libdb_crumb->record[5]."</a> &gt; ".$libdb_crumb->record[2]."";
		}
		if(preg_match('/bookmodify/i', $main) && preg_match('/id/i', $pg)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select a.book_recordid, b.id, b.category_id, b.category_name, c.book_recordid, c.book_title";
			$qry .=" from library_books_unit a, library_category b, library_books c";
			$qry .=" where a.book_recordid='".$_REQUEST['id']."' and c.book_recordid=a.book_recordid and b.category_id=c.category_id";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"category_details.php?kat=".$libdb_crumb->record[1]."\">".$libdb_crumb->record[3]."</a> &gt; ".$libdb_crumb->record[5]."";
		}
		if(preg_match('/bookmodify/i', $main) && preg_match('/accno/i', $pg)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select a.book_recordid, b.id, b.category_id, b.category_name, c.book_recordid, c.book_title";
			$qry .=" from library_books_unit a, library_category b, library_books c";
			$qry .=" where a.accession_no='".$_REQUEST['accno']."' and c.book_recordid=a.book_recordid and b.category_id=c.category_id";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"category_details.php?kat=".$libdb_crumb->record[1]."\">".$libdb_crumb->record[3]."</a> &gt; ".$libdb_crumb->record[5]."";
		}
		if(preg_match('/bookupdate/i', $main)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select b.id, a.category_id, a.book_title, a.book_isbn, b.category_id, b.category_name";
			$qry .=" from library_books a, library_category b where a.book_isbn='".$_REQUEST['isbn']."' and a.category_id=b.category_id";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"category_details.php?kat=".$libdb_crumb->record[0]."\">".$libdb_crumb->record[5]."</a> &gt; ".$libdb_crumb->record[2]."";
		}
		if(preg_match('/newbook/i', $main)) {
			echo "&nbsp;New Books Arrival";
		}
		if(preg_match('/booknew/i', $main)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select id, category_name from library_category where id=".$_REQUEST['kat']."";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"category_details.php?kat=".$libdb_crumb->record[0]."\">".$libdb_crumb->record[1]."</a> &gt; New Books";
		}
		if(preg_match('/search/i', $main)) {
			echo "&nbsp;Search";
		}
		if(preg_match('/report/i', $main)) {
			echo "&nbsp;Reports";
		}
		if(preg_match('/setting/i', $main)) {
			echo "&nbsp;Settings";
		}
		if(preg_match('/bookissue/i', $main)) {
			if(preg_match('list', $pg)) {
				echo "&nbsp;<a href=\"bookissue.php\">Issue Book</a> &gt; List of Issued Book";
			}
			else {
				echo "&nbsp;Issue Book";
			}
		}
		if(preg_match('/bookchecklist/i', $main)) {
			echo "&nbsp;Check List Issued Books";
		}
		if(preg_match('/bookreturn/i', $main)) {
			echo "&nbsp;Return Book";
		}
		if(preg_match('/receipt/i', $main)) {
			echo "&nbsp;Receipt";
		}
		if(preg_match('/lib_member/i', $main)) {
			if(preg_match('/detail/i', $pg)) {
				echo "&nbsp;<a href=\"lib_members.php\">Search Members</a> &gt; Member Detail";
			}
			else {
				echo "&nbsp;Search Members";
			}
		}
		if(preg_match('/pending/i', $main)) {
			if(preg_match('/approve/i', $pg)) {
				echo "&nbsp;<a href=\"bookpending.php\">List of Book Reservations</a> &gt; Approve Books";
			}
			else {
				echo "&nbsp;List of Book Reservations";
			}
		}
		if(preg_match('/bookreserve/i', $main)) {
			if(preg_match('/list/i', $pg)) {
				echo "&nbsp;<a href=\"bookreserve.php\">Reserve Book</a>";
			}
			else {
				echo "&nbsp;Reserve Book";
			}
		}
		
		if(preg_match('/historyborrowed/i', $main)) {
				$id = $_REQUEST['id'];
				echo "&nbsp;<a href=\"bookreserve.php?action=reserve&id=".$id."\">Reservation List</a> &gt;";
				echo "&nbsp;History Of Borrowed Books";
		}
		
	}
	}
	if (is_array($permissions) && in_array(502, $permissions) || in_array(503, $permissions)){
	if($main != '/library/index.php') {
		echo "&nbsp;<a href=\"index.php\">Main</a> &gt;";
		
		if (empty($pg) && preg_match('/category/i', $main)) {
			echo "&nbsp;Category";
		}
		if (preg_match('/st/i', $pg) && preg_match('/category/i', $main)) {
			echo "&nbsp;Category";
		}
		if(!empty($pg) && preg_match('/category/i', $main)) {
			if($main == '/library/pubcategorydetails.php' && preg_match('/kat/i',$pg)) {
				if(!empty($_REQUEST['kat'])) {
				$libdb_crumb = new Modules_sql;
				$qry = "select * from library_category where id=".$_REQUEST['kat']."";
				$libdb_crumb->query($qry);
				$libdb_crumb->next_record();
				
				}
			echo "&nbsp;<a href=\"pubcategory.php\">Category</a> &gt; ".$libdb_crumb->record[2]."";
			}	
		}
		if(preg_match('/bookdetails/i', $main)) {
			$libdb_crumb = new Modules_sql;
			$qry = "select b.id, a.category_id, a.book_title, a.book_isbn, b.category_id, b.category_name";
			$qry .=" from library_books a, library_category b where a.book_recordid='".$_REQUEST['id']."' and a.category_id=b.category_id";
			$libdb_crumb->query($qry);
			$libdb_crumb->next_record();
			echo "&nbsp;<a href=\"pubcategorydetails.php?kat=".$libdb_crumb->record[0]."\">".$libdb_crumb->record[5]."</a> &gt; ".$libdb_crumb->record[2]."";
		}
		if(preg_match('/search/i', $main)) {
			echo "&nbsp;Search";
		}
				
		if(preg_match('/bookreserve/i', $main)) {
			if(preg_match('list', $pg)) {
				echo "&nbsp;<a href=\"bookreserve.php\">Reserve Book</a>";
			}
			else {
				echo "&nbsp;Reserve Book";
			}
		}
		
		if(preg_match('/historyborrowed/i', $main)) {
				$id = $_REQUEST['id'];
				echo "&nbsp;<a href=\"bookreserve.php?action=reserve_confirm&id=".$id."\">Reservation List</a> &gt;";
				echo "&nbsp;History Of Borrowed Books";
		}
	}
	}
	?>
