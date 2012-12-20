<?= HTML::style("media/sledge/css/sledge.page.css") ?>

<?= $body_tag ?>

<iframe style="position:fixed; z-index: 10000; height: 60px; background: transparent;" id='s-page-topbar' src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>

<div id="sledge-dialogs">
	<div id="sledge-dialog-alerts">
		<p>&nbsp;</p>
	</div>
</div>

<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>