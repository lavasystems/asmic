<div class="navbar">
            <div class="container sp-cont">
                <div class="search-function">
                    <a href="#" class="search-trigger"><i class="fa fa-search"></i></a>
                </div>
                <a href="#" class="visible-sm visible-xs" id="menu-toggle"><i class="fa fa-bars"></i></a>
                <!-- Main Navigation -->
                <nav class="main-navigation dd-menu toggle-menu" role="navigation">
                    <ul class="sf-menu">
                        <li><a href="javascript:void(0)">Group & User Management</a>
                            <ul class="dropdown">
                                <li><a href="javascript:void(0)">Manage Group</a>
                                </li>
                                <li><a href="javascript:void(0)">Manage User</a>
                                </li>
                                <li><a href="javascript:void(0)">Search</a>
                                </li>
                                <li><a href="javascript:void(0)">Audit Trail</a>
								</li>
							</ul>
						</li>
                        <li><a href="javascript:void(0)">Event Management</a>
                            <ul class="dropdown">
                                <li><a href="about.html">Create New Event</a></li>
                                <li><a href="contact.html">Add Attendees</a></li>
                            </ul>
                        </li>
                        <li><a href="index.html">ASM Contact</a>
							<ul class="dropdown">
                                <li><a href="javascript:void(0)">Add Contact</a>					                                <li><a href="javascript:void(0)">Manage Contact</a></li>
                                <li><a href="javascript:void(0)">Add Category</a>								                                <li><a href="javascript:void(0)">Manage Category</a></li>
								<li><a href="javascript:void(0)">Add Area of Expertise</a>																<li><a href="javascript:void(0)">Manage Area of Expertise</a></li>
                                <li><a href="javascript:void(0)">Search</a></li>
                                <li><a href="javascript:void(0)">Export</a></li>
							</ul>													</li>
                        <li><a href="javascript:void(0)">Library</a>
                            <ul class="dropdown">
                                <li><a href="searchmaterial.php">Search</a></li>								                                <li><a href="addmaterial.php">Add Material</a></li>
                                <li><a href="listmaterial.php">Manage Material</a></li>
                                <li><a href="returnmaterial.php">Return Material</a></li>
                                <li><a href="addlibrarycategory.php">Add Category</a></li>
                                <li><a href="listlibrarycategory.php">Manage Category</a></li>								                                <li><a href="#">Others</a>									<ul class="dropdown">																				<li><a href="issuedlist.php">View Issued List</a></li>																				<li><a href="vehicle-comparision.html">Manage Reservation</a></li>																				<li><a href="libraryreport.php">Generate Report</a></li>																				<li><a href="vehicle-comparision.html">View Material by Loan Duration</a></li>																			</ul>																	</li>
                            </ul> 
                        </li>
                        <li><a href="javascript:void(0)">Gallery</a></li>
                        <li><a href="javascript:void(0)">Document Management System</a></li>
                    </ul>
                </nav> 
                <!-- Search Form -->
                <div class="search-form">
                    <div class="search-form-inner">
                        <form>
                            <h3>Find a Book with our Quick Search</h3>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Material Type</label>
                                            <select name="Body Type" class="form-control selectpicker">
                                                <option selected>Any</option>
                                                <option>Book</option>
                                                <option>Digital Book</option>
                                                <option>Journal</option>
                                                <option>CD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Categories</label>
                                            <select name="Model" class="form-control selectpicker">
                                                <option selected>Any</option>								
												<option value="1">Agriculture</option>								
												<option value="2">ASM Publication</option>								
												<option value="3">Auxiliary Sciences History</option>								
												<option value="4">BIbliography, Library Science, Information Resources (General)</option>								
												<option value="5">Education</option>								
												<option value="6">Fine Art</option>								
												<option value="7">General Work</option>								
												<option value="8">Generalities</option>								
												<option value="9">Geography, Anthropology</option>								
												<option value="10">History of Americas 1</option>								
												<option value="11">History of Americas 2</option>								
												<option value="12">Language and Literature</option>								
												<option value="13">Law</option>								
												<option value="14">Lc (Report)</option>								
												<option value="15">Medicine</option>								
												<option value="16">Military Science</option>								
												<option value="17">Music and Books on Music</option>								
												<option value="18">Naval Science</option>								
												<option value="19">Phylosophy, Psychology, Religion</option>								
												<option value="20">Political Science</option>								
												<option value="21">Science</option>								
												<option value="22">Social Science</option>								
												<option value="23">Technology</option>								
												<option value="24">Unassigned</option>								
												<option value="25">Uncategorise Science & Non-Science</option>								
												<option value="26">World History & History of Few Continents</option>	
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Book Code</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Title</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="inlineCheckbox1" value="option1"> Brand new only
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="inlineCheckbox2" value="option2"> Certified
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>ISBN No / ISSN No</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Author</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Status</label>
                                            <select name="Min Mileage" class="form-control selectpicker">
                                                <option selected>Any</option>
                                                <option>Available</option>
                                                <option>Issued</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="submit" class="btn btn-block btn-info btn-lg" value="Find my book">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>