<div id="b-tags">
	<span class="ui-icon ui-icon-tag" title="Tags"></span>

	<ul class="b-tags-list">
		<? foreach ($tags as $tag): ?>
			<li>
				<a href="<?= $tag->path ?>" title="Remove <?= $tag->path ?>" class="b-tags-remove"></a><span><?= $tag->path ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<input id="b-tags-add-name" />

	<button id="b-tags-add" class="boom-button" data-icon="ui-icon-boom-add">
			<?=__('Add')?> <?=__('tag')?>
	</button>
</div>