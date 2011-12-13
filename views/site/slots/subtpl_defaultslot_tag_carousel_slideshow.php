<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$toplevel_tag_rid = isset($_REQUEST['toplevel_tag']) && (is_int($_REQUEST['toplevel_tag']) || ctype_digit($_REQUEST['toplevel_tag'])) ? $_REQUEST['toplevel_tag'] : $toplevel_rid
?>
<div class="rel-toplevel-<?=$toplevel_tag_rid;?> cms-tag-library" style="margin-bottom: 2em; width: 45em; margin-left: 1em;">
	<strong>Click here to insert slideshow</strong>
</div>
