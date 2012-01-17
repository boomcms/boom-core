<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="sledge-tagmanager">

	<div class="sledge-tagmanager-main ui-helper-right">
		<div class="sledge-tagmanager-body ui-helper-clearfix">
			<div class="sledge-tagmanager-rightpane">
				<div class="content">
					&nbsp;
				</div>
			</div>

		</div>
	</div>
	<div class="sledge-tagmanager-sidebar ui-helper-left">

		<?= new View('ui/subtpl_tag_manager_search')?>

		<?//= new View('ui/subtpl_tag_tree')?>
	</div>
</div>

<script type="text/javascript">


	$.sledge.init('assets',  {
		person: {
			rid: <?= $person->id?>,
			firstname: '<?= $person->firstname?>',
			lastname: "<?= $person->lastname?>"
		}
	});

	$.sledge.tagmanager.assets.init({
		items: {
			tag: $.sledge.tagmanager.items.tag,
			asset: $.sledge.tagmanager.items.asset
		},
		options: {
			sortby: 'NULL',
			order: 'NULL',
			basetagRid: <?= $basetag_rid?>, 
			defaultTagRid: <?=$default_tag_rid?>,
			edition: '<?= $edition?>', 
			type: '<?= $type?>',
			selected: [<?= (count($selected) ? "'".implode('\',\'', $selected)."'" : '');?>], 
			types: [<?= (count($types) ? "'".implode('\',\'', $types)."'" : '');?>],
			excludeSmartTags: <?= (string) (int) $exclude_smart_tags?>,
			template: '<?= $template?>',
			allowedUploadTypes: [ '<?= implode('\', \'', Asset::$allowed_types)?>' ]
		}
	});
</script>
