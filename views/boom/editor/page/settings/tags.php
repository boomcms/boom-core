<div id="b-pagesettings-tags">
	<span class="ui-icon ui-icon-tag" title="Tags"></span>

	<ul class="b-tags-list">
		<? foreach ($current_tags as $tag): ?>
			<li>
				<a href="<?= $tag->path ?>" title="Remove <?= $tag->path ?>" class="b-tags-remove"></a><span><?= $tag->path ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<input id="b-pagesettings-tags-add-name" />

	<button id="b-pagesettings-tags-add" class="boom-button" data-icon="ui-icon-circle-plus">
			<?=__('Add')?> <?=__('tag')?>
	</button>
</div>