<ul id="b-page-settings-children-sort">
	<?php foreach ($children as $p): ?>
		<li data-id="<?= $p->getId() ?>">
			<span class="title"><?= $p->getTitle() ?></span>
		</li>
	<?php endforeach ?>
</ul>