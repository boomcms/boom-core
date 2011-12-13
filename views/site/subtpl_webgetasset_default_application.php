<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?if (preg_match('/\.docx$/',$webgetasset->filename)) {
	$subtype = 'Open Office XML document';
} else if (preg_match('/\.xls$/',$webgetasset->filename)) { 
	$subtype = 'Excel spreadsheet';
} else if (preg_match('/\.ppt$/',$webgetasset->filename)) { 
	$subtype = 'Powerpoint presentation';
} else {
	$subtype = $webgetasset->get_mime_subtype();
}
?>
<div>
<!--assetwrapperstart-->
<?  $new_src = '/_ajax/call/asset/get_asset/'.$webgetasset->rid.'/0/0/0/0/0/1'; ?>
<p class="inline-asset">
	<!--assetstart--><a class="download <?=$webgetasset->get_mime_subtype()?>" href="<?=$new_src?>" rel="<?=$webgetasset->rid?>" title="<?=$webgetasset->description?>">Download <?=$webgetasset->title?></a><!--assetend--> (<?=Misc::format_filesize($webgetasset->filesize)?>, <?=$subtype?>)
</p>
<!--assetwrapperend-->
</div>
