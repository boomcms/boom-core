<nav id="b-menu" class="pushy pushy-left">
	<img src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo" />
	<ul>
		<?php foreach ($items as $item): ?>
			<li>
				<a target='_top' href='<?= $item['url'] ?>'<?php if (isset($item['icon'])): ?> class="fa fa-2x fa-<?= $item['icon'] ?>"<?php endif ?>><?= Lang::get($item['title']) ?></a>
			</li>
		<?php endforeach ?>
	</ul>
</nav>
