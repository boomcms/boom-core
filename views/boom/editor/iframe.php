<?= $body_tag ?>

<iframe frameBorder="0" style="width: 100%; overflow: hidden; z-index: 10000; height: 0; background: transparent; position: fixed;<? if (Editor::instance()->state() !== Editor::EDIT): ?>border: none; width: 100px; right: 0; <? endif; ?>" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>

<div id="boom-dialogs">
	<div id="boom-dialog-alerts">
		<p>&nbsp;</p>
	</div>
</div>

<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>