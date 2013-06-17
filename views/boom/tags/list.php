<div id="b-tags">
	<h2>Existing tags</h2>
	<? if (isset($message)): ?>
		<span><?= $message ?></span>
	 <? endif ?>

	<ul class="b-tags-list">
		<? foreach ($tags as $tag): ?>
			<li>
				<a href="<?= $tag->name ?>" title="Remove <?= $tag->name ?>" class="b-tags-remove"></a><span><?= $tag->name ?></span>
			</li>
		<? endforeach; ?>
	</ul>

	<h2>Add tag</h2>
	<p><?= Kohana::message('boom', 'tags.add') ?></p>
	<input id="b-tags-add-name" />
</div>