<?= HTML::style("media/boom/css/boom.page.css") ?>

<?= $body_tag ?>

<iframe style="z-index: 10000; height: 30px; background: transparent; <? if (Editor::instance()->state() === Editor::EDIT): ?>position:fixed;<? else: ?>border: none; position: relative; width: 100px; float: right; <? endif; ?>" id='b-page-topbar' src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>

<div id="boom-dialogs">
	<div id="boom-dialog-alerts">
		<p>&nbsp;</p>
	</div>
</div>

<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>