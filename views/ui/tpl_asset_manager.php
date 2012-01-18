<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="sledge-tagmanager">

	<div class="sledge-tagmanager-main ui-helper-right">
		<div class="sledge-tagmanager-body ui-helper-clearfix">
			<div class="sledge-tagmanager-rightpane">
				<div class="content">
					Asset folders
				</div>
			</div>

		</div>
	</div>
	<div class="sledge-tagmanager-sidebar ui-helper-left">

		<?= new View('ui/subtpl_tag_manager_search')?>

		<ul class="users tree">
		<?
			foreach ($assets as $asset):
				?>
					<li>
						<div class="row">
							<div class="col1 check">
								<input type="checkbox" name="massaction" id="ma<?=$asset->id?>" />
							</div>						
							<div class="col2 title">
								<a href='/cms/assets/view/<?= $asset->id ?>'>
									<?= $asset->title ?>
								</a>
							</div>
						</div>
					</li>
				<?
			endforeach;
		?>
		</ul>
	</div>
</div>
<?/*
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
*/?>
