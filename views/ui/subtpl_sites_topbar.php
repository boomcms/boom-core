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
		<div id="sledge-page-actions">
			<button id="sledge-page-save" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				<span class="ui-button-text">
					<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
					Save
				</span>
			</button>
			<div id="sledge-page-undo-splitbutton" class="ui-splitbutton ui-buttonset">
				<button id="sledge-page-undo" disabled="disabled" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left">
					<span class="ui-button-text">
						<span class="ui-button-icon-primary ui-icon ui-icon-cancel"></span>
						Undo
					</span>
				</button>
				<button disabled="disabled" class="ui-button ui-widget ui-state-default ui-button-icon-only sledge-button ui-corner-right ui-splitbutton-hitarea">
					<span class="ui-button-icon-primary ui-icon ui-icon-triangle-1-s"></span>
					<span class="ui-button-text">Select an action</span>
				</button>
			</div>

			<button id="sledge-page-delete" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				<span class="ui-button-text">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
					Delete
				</span>
			</button>
			<button id="sledge-page-publish" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				<span class="ui-button-text">
					<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
					Publish
				</span>
			</button>

		</div>


		<div id="sledge-page-metadata" class="ui-helper-right">
			<div id="sledge-page-settings-menu" class="ui-splitbutton ui-buttonset">
				<button id="sledge-page-settings" class="sledge-button sledge-splitbutton ui-button-text-icons ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left ui-corner-right ui-splitbutton-hitarea">
					<span class="ui-button-text">
						<span class="ui-button-icon-primary ui-icon ui-icon-wrench"></span>
						Settings
						<span class="ui-button-icon-secondary ui-icon ui-icon-triangle-1-s"></span>
					</span>
				</button>
			</div>
			<button id="sledge-page-addpage" class="sledge-button ui-helper-left ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				<span class="ui-button-text">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-plus"></span>
					Add page
				</span>
			</button>
			<div id="sledge-page-preview-splitbutton" class="ui-splitbutton ui-buttonset">
				<button id="sledge-page-preview" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left">
					<span class="ui-button-text">
						<span class="ui-button-icon-primary ui-icon ui-icon-search"></span>
						Preview
					</span>
				</button>
				<button class="ui-button ui-widget ui-state-default ui-button-icon-only sledge-button ui-corner-right ui-splitbutton-hitarea">
					<span class="ui-button-icon-primary ui-icon ui-icon-triangle-1-s"></span>
					<span class="ui-button-text">Select an action</span>
				</button>
			</div>
			<button id="sledge-page-versions" class="sledge-button ui-helper-left ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				<span class="ui-button-text">
					<span class="ui-button-icon-primary ui-icon ui-icon-transferthick-e-w"></span>
					Versions
				</span>
			</button>
		</div>
	</div>
	
	<div id="sledge-topbar-pagesettings" class="ui-helper-clearfix">
		<div class="ui-helper-center">
			<?= new View('ui/subtpl_sites_pagesettings');?>
		</div>
	</div>

	<div id="sledge-topbar-revisions" class="ui-helper-clearfix">
		This page version is <strong><?=$page->getVersionStatus()?></strong>. 
		<?
			/*
			if ($page_type == 'a draft' && $published_page->rid):
				echo "A <a href='#' id='sledge-topbar-status-change'>published version</a> exists for this page.";
			endif;
			*/
		?>
	</div>
</div>
