<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<form action="/search" method="post" id="searchform">
	<fieldset>
		<label for="searchbox"><? echo __( 'Search' ); ?></label>
		<input type='hidden' name='postbox' value='search' />
		<input type="text" name="search" id="searchbox" />
		<input type="submit" value="Find" class="button" />			
	</fieldset>
</form>
