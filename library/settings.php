<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
session_start();
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include("class.php");
include '../inc/pagehead.php';
?>
<body class="home">
<!--[if lt IE 7]>
  <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
<![endif]-->
<div class="body">
  <!-- Start Site Header -->
  <div class="site-header-wrapper">
        <header class="site-header">
            <div class="container sp-cont">
                <div class="site-logo">
                    <h1><a href="<?php echo $app_absolute_path ?>index.php"><img src="<?php echo $app_absolute_path ?>images/company_logo.png" alt="Logo"></a></h1>
                </div>
                <div class="header-right">
                    <div class="topnav dd-menu">
                        <ul id="menu-top-menu" class="top-navigation sf-menu sf-js-enabled">
                            <li><a href="<?php echo $app_absolute_path ?>index.php"><i class="fa fa-home"></i> Home</a></li>
                            <?php if(isset($_SESSION['usr_id'])): ?>
                            <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=user&amp;do=password"><i class="fa fa-key"></i> Change Password</a></li>
                            <li><a href="<?php echo $app_absolute_path ?>index.php?do=logout"><i class="fa fa-lock"></i> Logout</a></li>
                            <?php endif; ?>
                        </ul>                    
                    </div>                
                </div>
            </div>
        </header>
        <!-- End Site Header -->
        <div class="navbar">
            <div class="container sp-cont">
                <div class="search-function">
                    <a href="#" class="search-trigger"><i class="fa fa-search"></i></a>
                </div>
                <a href="#" class="visible-sm visible-xs" id="menu-toggle"><i class="fa fa-bars"></i></a>
                <?php include '../inc/navigation.php'; ?>
                <?php include '../inc/search.php'; ?>
            </div>
        </div>
    </div>
    <!-- Start Body Content -->
    <div class="main" role="main">
      <div id="content" class="content full">
            <div class="container">
              <div class="dashboard-wrapper">
                <!-- Visitor's View -->
                <div class="row">
<h2>Loan Duration</h2>
		  <?php
		  switch ($_GET['action']) {
		  	case "done" :
				setting_success();
			break;
			default :
				setting();
			break;
		  }
		  ?>
<?php
function setting () {
	 $libdb = new Modules_sql;
	 $qry_str = "select * from library_settings;";
	 $libdb->query($qry_str);
	 $libdb->next_record();
	 $var_issue_period = (empty($libdb->record[0]))?"":$libdb->record[0];
	 $var_cards = (empty($libdb->record[2]))?"":$libdb->record[2];
?>
<div class="alert alert-info">Please fill in the following Loan Duration details</div>
	<form action="functions.php?action=settings" method="post" name="settings" id="settings" onSubmit="return validateField();" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-2 control-label">Issue Period</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input class="form-control" name="issue_period" type="text" id="issue_period" value="<?php echo $var_issue_period; ?>">
					<div class="input-group-addon">day(s)</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Cards Per-Member</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input class="form-control" name="cards_issued" type="text" id="cards_issued" value="<?php echo $var_cards; ?>">
					<div class="input-group-addon">card(s)</div>
				</div>
			</div>
		</div>
	<?php
		$lib_set = new Modules_sql;
		$qry = "select count(*) from library_settings";
		$lib_set->query($qry);
		$lib_set->next_record();
		if(!$lib_set->record[0]) {
	?>
		<input type="submit" name="submit" class="btn btn-primary" value="Submit">
		<a class="btn btn-default" href="index.php">Cancel</a> 
    <?php }else{ ?>
    	<input type="submit" class="btn btn-primary" value="Update">
    	<a class="btn btn-default" href="index.php">Cancel</a> 
    <?php } ?>
   	</form>
<?php } ?>

<?php
function setting_success() {
$libdb = new Modules_sql;
$qry_str = "select * from library_settings;";
$libdb->query($qry_str);
$libdb->next_record();
	echo "<form action=\"index.php\">";
	echo "<div class=\"alert alert-success\">Your loan duration has been updated</div>";
	echo "<p>Issue Period : ".$libdb->record[0]."</p>";
	echo "<p>Card(s) per-Member : ".$libdb->record[2]."</p>";
	echo "<input type=\"submit\" name=\"submit\" class=\"btn btn-default\" value=\"Okay\">";
	echo "</form></td>";
}
?>
<script language="javascript">
	function validateField() {
		
    	var form = document.settings;
    
		if (form.elements["issue_period"].value==0) {
			alert( "You must enter days of issue period" );
			form.elements["issue_period"].focus();
			return false;
		}
	
		if (form.elements["cards_issued"].value == "") {
			alert( "You must enter numbers of card per-member" );
			form.elements["cards_issued"].focus();
			return false;
		}
	}

</script>
</div>
                    <?php if(isset($_SESSION)):
                        //var_dump($_SESSION);
                    endif; ?>
              </div>
        </div>
    <!-- End Body Content -->

  <?php include '../inc/footer.php'; ?>
  
    <!-- End site footer -->
    <a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include '../inc/js.php'; ?>
</body>
</html>