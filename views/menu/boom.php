<div id="boom-loader"></div>
<div id="boom-topbar-useractions">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="border:0;margin-top:1px;">
			<li class="ui-corner-top"><a href="#" id="boom-profile"><img src="<?= URL::gravatar($person->email, 18) ?>" alt="<?= $person->name ?>" /></a></li>
		<li class="ui-corner-top"><a href="/cms/logout">Log out</a></li>
	</ul>
</div>

<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="float:left;">

	<? foreach ($menu_items as $item): ?>
		<li class='ui-corner-top'>
			<a href='<?= $item['url'] ?>'><?=__($item['title'])?></a>
		</li>
	<? endforeach; ?>

</ul>