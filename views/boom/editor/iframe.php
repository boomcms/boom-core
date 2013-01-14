<?= HTML::style("media/boom/css/boom.page.css") ?>

<?= $body_tag ?>

<iframe style="overflow: hidden; z-index: 10000; height: 30px; background: transparent; position: fixed;<? if (Editor::instance()->state() !== Editor::EDIT): ?>border: none; width: 100px; right: 0; <? endif; ?>" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>

<div id="boom-dialogs">
	<div id="boom-dialog-alerts">
		<p>&nbsp;</p>
	</div>
</div>

<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>