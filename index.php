<?php
include_once("global_config.php");
include_once("includes/functions.php");
include_once("includes/database.php");
include_once("classes/user.php");
include 'inc/pagehead.php'; ?>
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
                <?php include 'inc/navigation.php'; ?>
                <?php include 'inc/search.php'; ?>
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
                  <?php
                    if (empty($_SESSION['usr_id']) || $_REQUEST['do'] == 'login') { ?>
                    <div class="col-md-3">
                      <div class="block">
                        <!-- Horizontal Form Title -->
                        <div class="block-title">
                          <h2><strong>Login</strong> or Register</h2>
                        </div>
                        <!-- END Horizontal Form Title -->

                        <!-- Horizontal Form Content -->
                        <form name="frmLogin" action="user/login.php" method="post" class="form-horizontal form-bordered">
                          <div class="form-group">
                            <div class="col-md-12">
                              <input type="text" name="username" class="form-control" placeholder="Enter Username..">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-12">
                              <input type="password" name="password" class="form-control" placeholder="Enter Password..">
                              <input type="hidden" name="do" value="login">
                            </div>
                          </div>
                          <div class="form-group form-actions">
                            <div class="col-md-12">
                            <div class="danger"><?php echo $err_msg; ?></div>
                              <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-user"></i> Login</button>
                              <button type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> Reset</button>
                            </div>
                          </div>
                        </form>
                        <!-- END Horizontal Form Content -->
                      </div>
                    </div>
                    <?php }elseif ($_REQUEST['do'] == 'logout'){
                            include_once("user/logout.php");
                        }elseif (!empty($_REQUEST['err'])){
                            include_once("error.php");
                        }else{
                          include_once("includes/modules.php");
                    } ?>
	                </div>
                    <?php if(isset($_SESSION)):
                        //var_dump($_SESSION);
                    endif; ?>
	            </div>
   			</div>
    <!-- End Body Content -->

	<?php include 'inc/footer.php'; ?>
	
    <!-- End site footer -->
  	<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include 'inc/js.php'; ?>
</body>
</html>