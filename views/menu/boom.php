<ul id="b-menu">
	<? foreach ($menu_items as $item): ?>
		<li class='ui-corner-top'>
			<a target="_top" href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
		</li>
	<? endforeach; ?>
</ul>