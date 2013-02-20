<div id="b-tags">
	<span class="ui-icon ui-icon-boom-tag" title="Tags"></span>

	<ul class="b-tags-list">
		<? foreach ($tags as $tag): ?>
			<li>
				<a href="<?= $tag->name ?>" title="Remove <?= $tag->name ?>" class="b-tags-remove"></a><span><?= $tag->name ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<input id="b-tags-add-name" class="b-filter-input" />

	<button id="b-tags-add" class="boom-button" data-icon="ui-icon-boom-add">
			<?=__('Add')?> <?=__('tag')?>
	</button>
</div>