<div id="b-menu">
	<span>
		<button id="b-menu-button" class="ui-button boom-button" data-icon="ui-icon-boom-menu">
			<?= __('Menu') ?>
		</button>
	</span>

	<ul>
		<? foreach ($menu_items as $item): ?>
			<li>
				<a target='_top' href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
			</li>
		<? endforeach; ?>
	</ul>
</div>