<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$toplevel_tag_rid = isset($_REQUEST['toplevel_tag']) && (is_int($_REQUEST['toplevel_tag']) || ctype_digit($_REQUEST['toplevel_tag'])) ? $_REQUEST['toplevel_tag'] : $toplevel_rid
?>
<div class="rel-toplevel-<?=$toplevel_tag_rid;?> cms-tag-library">
	<strong>Click here to insert a gallery</strong>
</div>
