<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="sledge-tagmanager">


	<div class="sledge-tagmanager-main ui-helper-right">
		<div class="sledge-tagmanager-body ui-helper-clearfix">
			<div class="sledge-tagmanager-rightpane">
				<div class="content">
					<ul class="groups tree">
					<?
						// foreach
							?>
								<li>
									<div class="row">
										<div class="col1 check">
											<input type="checkbox" name="massaction" id="ma<?= "\$role->id" ?>" />
										</div>
										<div class="col2 name">
											<a title="<?= "\$role->name" ?>" id="person_<?="\$role->id";?>" href="/cms/people/view/<?="\$role->id"?>" class="user">
												<?= "\$role->name" ?>
											</a>
										</div>
									</div>
								</li>
							<?
						// endforeach;
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="sledge-tagmanager-sidebar ui-helper-left">

		<?= new View('ui/subtpl_tag_manager_search');?>

		<ul class="users tree">
		<?
			foreach ($people as $person):
				?>
					<li>
						<div class="row">
							<div class="col1 check">
								<input type="checkbox" name="massaction" id="ma<?=$person->id?>" />
							</div>
							<div class="col2 date">
								<?= $person->getAuditTime() ?>
							</div>
							<div class="col3 username">
								<a title="<?= $person->getName() ?>" id="person_<?=$person->id;?>" href="/cms/people/view/<?=$person->id?>" class="user">
									<?= $person->getName() ?>
								</a>
							</div>
							<div class="col6 email">
								<?= $person->emailaddress;?>
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


	$.sledge.init('people',  {
		person: {
			rid: <?= $person->id?>,
			firstname: '<?= $person->firstname?>',
			lastname: "<?= $person->lastname?>"
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
			selected: [<?= (count($this->selected) ? "'".implode('\',\'', $selected)."'" : '');?>], 
			types: [<?= (count($this->types) ? "'".implode('\',\'', $types)."'" : '');?>],
			excludeSmartTags: <?= (string) (int) $this->exclude_smart_tags?>,
			template: '<?= $this->template?>',
			allowedUploadTypes: [ '<?= implode('\', \'', $allowed_types)?>' ]
		}
	});
</script>
*/?>
