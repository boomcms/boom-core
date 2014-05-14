<ul id="b-page-settings-children-sort">
	<? foreach ($children as $p): ?>
		<li data-id="<?= $p->id ?>">
			<span class="title"><?= $p->getTitle() ?></span>
		</li>
	<? endforeach ?>
</ul>