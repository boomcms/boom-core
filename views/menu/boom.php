<div id="boom-loader"></div>
<div id="boom-topbar-useractions">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="border:0;margin-top:1px;">
			<li class="ui-corner-top"><img src="<?= URL::gravatar($person->email, array('s' => 18), Request::$initial->secure()) ?>" alt="You are logged in as <?= $person->name ?>" title="You are logged in as <?= $person->name ?>" /></li>
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