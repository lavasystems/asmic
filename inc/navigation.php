            <nav class="main-navigation dd-menu toggle-menu" role="navigation">
                    <ul class="sf-menu">
                        <?php if(isset($_SESSION['usr_id'])): ?>
                        <li class=""><a href="<?php echo $app_absolute_path ?>index.php?mod=user" class="sf-with-ul"><i class="fa fa-users"></i> Group &amp; User Management</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=group">Manage Group</a>
                                </li>
                                <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=user">Manage User</a>
                                </li>
                                <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=search">Search</a>
                                </li>
                                <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=audittrail">Audit Trail</a>
                                </li>
                            </ul>
                        </li>
                        <li class=""><a href="<?php echo $app_absolute_path ?>contact/list.php" class="sf-with-ul"><i class="fa fa-user"></i> ASM Contact</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo $app_absolute_path ?>contact/edit.php?mode=new">Add Contact</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>contact/list.php">Manage Contact</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>">Add Category</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>contact/catman.php">Manage Category</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>">Add Area of Expertise</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>contact/areaman.php">Manage Area of Expertise</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>">Search</a></li>
                                <li><a href="<?php echo $app_absolute_path ?>contact/export.php">Export</a></li>
                            </ul>
                        </li>
                        <li class=""><a href="<?php echo $app_absolute_path ?>library/index.php" class="sf-with-ul"><i class="fa fa-book"></i> Library</a>
                            <ul class="dropdown">
                                <?php if (isAllowed(array(501), $_SESSION['permissions'])){ ?>
                                <li><a href="#" class="sf-with-ul">Administration</a>
                                    <ul class="dropdown">
                                        <li><a href="<?php echo $app_absolute_path ?>library/index.php" class="link_menumodule">Main</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/category.php" class="link_menumodule">Category</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/bookissue.php" class="link_menumodule">Issue Book</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/bookreturn.php" class="link_menumodule">Return Book</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/search_books.php" class="link_menumodule">Search</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/reports.php" class="link_menumodule">Reports</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/settings.php" class="link_menumodule">Loan Duration</a></li>
                                    </ul>
                                </li>
                                <?php } ?>
                                <li><a href="#" class="sf-with-ul">Category</a>
                                    <ul class="dropdown">
                                        <li><a href="<?php echo $app_absolute_path ?>library/category.php">Add Category</a></li>
                                    </ul>
                                </li>
                                <li><a href="#" class="sf-with-ul">Books</a>
                                    <ul class="dropdown">
                                        <li><a href="<?php echo $app_absolute_path ?>library/booknew.php">Add New Book</a></li>             
                                        <li><a href="<?php echo $app_absolute_path ?>library/bookissue.php">Issue Book</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/bookchecklist.php">Check List</a></li>
                                        <li><a href="<?php echo $app_absolute_path ?>library/bookreturn.php">View Material by Loan Duration</a></li>
                                    </ul>
                                </li>
                                <li><a href="reservationlist.php">Manage Reservation</a></li>
                                <li><a href="libraryreport.php">Generate Report</a></li>
                            </ul> 
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>