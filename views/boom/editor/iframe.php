<?= HTML::style("media/boom/css/boom.page.css") ?>

<?= $body_tag ?>

<iframe style="position:fixed; z-index: 10000; height: 60px; background: transparent;" id='s-page-topbar' src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>

<div id="boom-dialogs">
	<div id="boom-dialog-alerts">
		<p>&nbsp;</p>
	</div>
</div>

<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>