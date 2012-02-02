<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all"> 
	<div id="sledge-loader"></div>
        <div id="sledge-topbar-useractions">
                <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="border:0;margin-top:1px;">
                        <li class="ui-state-default ui-corner-top"><a href="#">Profile</a></li>
                        <li class="ui-state-default ui-corner-top"><a href="/cms/logout">Log out</a></li>
                </ul>
        </div>

	<?= new View('ui/subtpl_topbar_nav')?>

	<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
		<div id="sledge-page-actions" class="ui-helper-right">
			<button id="sledge-tagmanager-create-person" class="sledge-button ui-button-text-icon" onclick="window.location='/cms/people/add'">
				<span class="ui-button-icon-primary ui-icon ui-icon-person"></span>
				New person
			</button>
		</div>
	</div>
</div>

