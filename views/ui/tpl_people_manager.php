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

		<?= new View('cms/ui/subtpl_tag_manager_search');?>

		<?= new View('cms/ui/subtpl_tag_tree')?>
	</div>
</div>

<?
	$allowed_types = array();
	$types = O::fa('asset_type')->where("allowed_to_upload = 't' and extension != ''")->find_all();
	foreach($types as $type){
		array_push($allowed_types, $type->extension);
	}
?>
<script type="text/javascript">


	$.sledge.init('people',  {
		person: {
			rid: <?= $this->person->rid?>,
			firstname: '<?= $this->person->firstname?>',
			lastname: "<?= $this->person->lastname?>"
		}
	});

	$.sledge.tagmanager.people.init({
		items: {
			tag: $.sledge.tagmanager.items.tag,
			person: $.sledge.tagmanager.items.person
		},
		options: {
			basetagRid: <?= $this->basetag_rid?>, 
			defaultTagRid: <?=$this->default_tag_rid?>,
			edition: '<?= $this->edition?>', 
			type: '<?= $this->type?>',
			selected: [<?= (count($this->selected) ? "'".implode('\',\'', $this->selected)."'" : '');?>], 
			types: [<?= (count($this->types) ? "'".implode('\',\'', $this->types)."'" : '');?>],
			excludeSmartTags: <?= (string) (int) $this->exclude_smart_tags?>,
			template: '<?= $this->template?>',
			allowedUploadTypes: [ '<?= implode('\', \'', $allowed_types)?>' ]
		}
	});
</script>
