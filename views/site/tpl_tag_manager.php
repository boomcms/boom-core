<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<?
	$viewvars = array();
	$viewvars['type'] = $type;
	$viewvars['basepath'] = $basepath;
	$viewvars['basetag'] = $basetag;
	$viewvars['selectedtag'] = $selectedtag;
	$viewvars['itemtypes'] = $itemtypes;
	$viewvars['edition'] = $edition;
	$viewvars['item_view_type'] = $item_view_type;
	$viewvars['sort_by'] = $sort_by;
	$viewvars['roottag'] = $roottag;
	$viewvars['smartfolderstag'] = $smartfolderstag;
	$viewvars['stringitemtypes'] = implode("-", $viewvars['itemtypes']);
?>

<script type="text/javascript">
	var displaytags = [];
        <?
		$count=0; foreach (Tag::gettags($basetag->rid) as $children_of_assets) {
			if (isset($children_of_assets)) {
				if ($children_of_assets->name != 'Smart folders') {
					?>displaytags.push(<?echo $children_of_assets->rid?>);<?
				}
			}
			$count++;
		}
		if ($count==0 && $basetag->rid == $selectedtag->rid) {?>displaytags.push(<?echo $basetag->rid?>);<?}
	?>
	$(function(){	
		Loader.load("TagManager", {
			type : "<?=$viewvars['type']?>",				// used to create template name
			basepath : "<?=$viewvars['basepath']?>",			// used to construct uri's relevant to where this tag manager is
			basetag : "<?=$viewvars['basetag']->rid?>",			// where to start building the tree
			selectedtag : "<?=$viewvars['selectedtag']->rid?>",		// which tag is open by default
			itemtypes : [<?$count = 0; foreach ($viewvars['itemtypes'] as $itemtype) {$itemtypes[$count] = "'" . $itemtype . "'";} echo join(",", $itemtypes)?>],	// what types of thing associated with a tag are we interested in (eg: all, asset, page, user, message, tag, etc)
			stringitemtypes : "<?=$viewvars['stringitemtypes']?>",
			edition : "<?=$viewvars['edition']?>",			// cms or site?
			item_view_type : "<?=$viewvars['item_view_type']?>",	// thumbnail or list?
			sort_by : "<?=$viewvars['sort_by']?>",			// name or date?
			displaytags : displaytags,
			modal : 0
		});
	});
</script>
<div id="components">
        <div id="progressbar"></div>
</div>

<div id="hiddenpanes" class="hidden">
        <?= new View('site/subtpl_tag_manager_panes')?>
</div>
