<div id="boom-loader"></div>

<ul id="boom-nav" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">

	<? foreach ($menu_items as $item): ?>
		<li class='ui-corner-top'>
			<a href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
		</li>
	<? endforeach; ?>

</ul>