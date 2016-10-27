<form name="form2" method="post" action="<?php echo(FILE_SEARCHNEW); ?>" class="form-inline">
	<div class="input-group">
		<input name="goTo" type="text" class="form-control" placeholder="Enter any text to search">
		<div class="input-group-addon"><i class="fa fa-search"></i></div>
	</div>
		<input type="hidden" name="Submit2" value="search">
	<button type="submit" class="btn btn-primary">Search</button>
	<?php if ($_SERVER['PHP_SELF'] != "/contact/detsearch.php"){?>
	or <a class="btn btn-default" href="detsearch.php">Advanced search</a>
	<?php } ?>
</form>