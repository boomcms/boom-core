<div id="b-menu">
	<span>
		<?= \Boom\UI::button('menu', __('Menu'), array('id' => 'b-menu-button')) ?>
	</span>

	<ul>
		<? foreach ($menu_items as $item): ?>
			<li>
				<a target='_top' href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
			</li>
		<? endforeach; ?>
	</ul>
</div>