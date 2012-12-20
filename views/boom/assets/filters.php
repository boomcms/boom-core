<div class="s-items-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
	<h3 class="ui-helper-reset">
		<span class="ui-icon ui-icon-carat-1-e ui-helper-left"></span>
		<?=__('Filters')?>
	</h3>
</div>
<div class="sledge-box ui-widget ui-corner-all">
	<ul class="ui-helper-clearfix sledge-tree sledge-tree-noborder">
		<li><a id='tag_all' href='#tag/0'><?=__('All assets')?></a></li>
		<li>
			<input type='text' id="s-assets-filter-title" />
		</li>
		<li><a><?=__('Uploaded by')?></a>
			<ul class="ui-helper-hidden">
				<? foreach ($uploaders as $uploader): ?>
					<li><a href='#uploaded_by/<?= $uploader['id'] ?>'><?= $uploader['name'] ?></a></li>
				<? endforeach ?>
			</ul>
		</li>
		<li><a><?=__('Type')?></a>
			<ul class="ui-helper-hidden">
				<? foreach ($types as $type): ?>
					<li><a href='#type/<?= $type ?>'><?= $type ?></a></li>
				<? endforeach ?>
			</ul>
		</li>
		<li><a id='tag_all' href='#rubbish/rubbish'><?=__('Rubbish')?></a></li>
	</ul>
</div>