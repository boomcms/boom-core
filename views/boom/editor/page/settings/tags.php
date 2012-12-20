<div id="s-pagesettings-tags">
	<span class="ui-icon ui-icon-tag" title="Tags"></span>

	<ul class="s-tags-list">
		<? foreach ($current_tags as $tag): ?>
			<li>
				<a href="<?= $tag->path ?>" title="Remove <?= $tag->path ?>" class="s-tags-remove"></a><span><?= $tag->path ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<input id="s-pagesettings-tags-add-name" />

	<button id="s-pagesettings-tags-add" class="sledge-button" data-icon="ui-icon-circle-plus">
			<?=__('Add')?> <?=__('tag')?>
	</button>
</div>