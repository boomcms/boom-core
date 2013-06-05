<ul id="b-page-childen-sort" class="sort_list">
	<? foreach ($children as $p): ?>
		<li data-id="<?= $p->id ?>">
			<span><?= $p->version()->title ?></span>
		</li>
	<? endforeach ?>
</ul>