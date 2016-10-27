<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
session_start();
$id = $_SESSION['usr_id'];
$permission = $_SESSION['permissions'];
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501, 502,503), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include '../inc/pagehead.php';
include("class.php");
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
                <h2>Search Books</h2>
    <?php
			switch ($_GET['mode'])		{
			case "advance" :
				advance_search();
			break;
			default :
				main_search();
			break;
			}
			
		  if (isAllowed(array(502,503), $_SESSION['permissions'])){
        include("pubrightbar.php");
      } ?>
<?php
function main_search() {
global $app_absolute_path, $root_images_folder;
?>
<div class="row">
  <div class="col-lg-4 col-lg-offset-4">
    <form name="form1" method="post" action="search_result.php">
      <div class="form-group">
        <input name="keyword" type="text" id="keyword" class="form-control">
        <input type="submit" class="btn btn-primary" value="Search"> 
        <a class="btn btn-default" href="search_books.php?page=advance">Advanced Search</a>
      </div>
    </form>
  </div>
</div>
<?php
}

  function advance_search() {
  global $app_absolute_path, $root_images_folder;

	 $libdb = new Modules_sql;
	 $qry_str = "select category_id, category_name from library_category order by category_name asc;";
	 $libdb->query($qry_str);

	?>
  <div class="row">
    <div class="col-lg-6 col-lg-offset-3">
      <form name="form1" method="post" action="search_result.php?mode=advance">
      <dl class="dl-horizontal">
        <dt>Subject</dt>
        <dd><input class="form-control" name="subject" type="text" id="subject"></dd>
        <dt>Categories</dt>
        <dd><select class="form-control" name="category" id="select">
          <option value="">- Choose Category -</option>
          <?php
					while($libdb->next_record()) {
						echo "<option value=\"".$libdb->record[0]."\">".$libdb->record[1]."</option>";
				}
				?>
        </select></dd>
        <dt>Book Code</dt>
        <dd><input class="form-control" name="book_code" type="text" id="book_code"></dd>
        <dt>ISBN No</dt>
        <dd><input class="form-control" name="isbn" type="text" id="isbn"></dd>
        <dt>Title</dt>
        <dd><input class="form-control" name="book_title" type="text" id="book_title"></dd>
        <dt>Author</dt>
        <dd><input class="form-control" name="author" type="text" id="author"></dd>
        <dt>Status</dt>
        <dd><select class="form-control" name="status" id="status">
          <option value="1">ALL</option>
          <option value="2">Available</option>
          <option value="3">Issued</option>
        </select>
        </dd>
        </dl>
        <input type="submit" class="btn btn-primary" value="Search">
        <a class="btn btn-primary" href="search_books.php?mode=search">Simple Search</a> 
        <?php if (isAllowed(array(501), $_SESSION['permissions'])){ ?>
        <a class="btn btn-default" href="lib_members.php">Members Search</a>
        <?php } ?>
  </div>
</div>
      <?php } ?>
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