<nav id="b-menu" class="pushy pushy-left">
	<img src="/public/boom/img/logo.png" alt="BoomCMS Logo" />
	<ul>
		<? foreach ($menu_items as $item): ?>
			<li>
				<a target='_top' href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
			</li>
		<? endforeach; ?>
	</ul>
</nav>