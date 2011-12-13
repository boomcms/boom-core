<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div>
<!--assetwrapperstart-->
<?  $new_src = class_exists('Cms_page_manager_Controller', false) ? '/cms_page_manager/get_asset/'.$webgetasset->rid : '/get_asset/'.$webgetasset->rid; ?>
<p>
	<!--assetstart--><img style="display:block;border:1px solid #999;padding:0px;margin:0px;line-height:1.0;" src="<?=$new_src?>" alt="<?=$webgetasset->description?>" /><!--assetend-->
</p>
<!--assetwrapperend-->
</div>
