<div class="b-items-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
	<a class="ui-helper-right" id='tag_all' href='#tag/0'><?=__('All assets')?></a>

	<h3 class="ui-helper-reset">
		<?=__('Filters')?>
	</h3>
</div>
<div class="boom-box ui-widget ui-corner-all ui-state-default">
	<input type='text' id="b-assets-filter-title" placeholder="Type name - e.g. logo.png" />
	<ul class="ui-helper-clearfix boom-tree boom-filter-tree boom-tree-noborder">
		<li><a><?=__('Uploaded by')?></a>
			<ul class="ui-helper-hidden">
				<? foreach ($uploaders as $id => $name): ?>
					<li><a href='#uploaded_by/<?= $id ?>'><?= $name ?></a></li>
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