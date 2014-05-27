<?= \Boom\UI::button('menu', __('Menu'), array('id' => 'b-menu-button', 'class' => 'menu-btn')) ?>

<nav id="b-menu" class="pushy pushy-left">
	<ul>
		<? foreach ($menu_items as $item): ?>
			<li>
				<a target='_top' href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
			</li>
		<? endforeach; ?>
	</ul>
</nav>