<div id="b-tags">
	<div>
		<span class="ui-icon ui-icon-boom-tag" title="Tags"></span>
		<? if (isset($message)): ?>
			<span><?= $message ?></span>
		 <? endif ?>
	</div>

	<ul class="b-tags-list">
		<? foreach ($tags as $tag): ?>
			<li>
				<a href="<?= $tag->name ?>" title="Remove <?= $tag->name ?>" class="b-tags-remove"></a><span><?= $tag->name ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<input class="b-filter-input" id="b-tags-add-name" />

	<button id="b-tags-add" class="boom-button" data-icon="ui-icon-boom-add">
			<?=__('Add')?> <?=__('tag')?>
	</button>
</div>