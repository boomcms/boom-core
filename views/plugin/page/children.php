<ul>
	<? foreach ($pages as $p): ?>
		<li>
			<a href="<?= $p->url() ?>"><?= $p->title ?></a>
		</li>
	<? endforeach; ?>
</ul>