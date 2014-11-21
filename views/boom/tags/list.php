<div id="b-tags">
	<h2>Existing tags</h2>
	<?php if (isset($message)): ?>
		<p class="none"><?= $message ?></p>
	 <?php endif ?>

	<ul class="b-tags-list">
		<?php foreach ($tags as $tag): ?>
			<li>
				<a href="<?= $tag->getName() ?>" title="Remove <?= $tag->getName() ?>" class="b-tags-remove" data-tag_id="<?= $tag->getId() ?>"></a><span><?= $tag->getName() ?></span>
			</li>
		<?php endforeach ?>
	</ul>

	<h2>Add tag</h2>
	<p><?= Kohana::message('boom', 'tags.add') ?></p>
	<input type="text" id="b-tags-add-name" />
</div>