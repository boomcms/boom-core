<?= $body_tag ?>

<style>
	.b-editable:hover {
		background-color: #FFD200;
		cursor: pointer;
		border: 2px dotted black !important;
		color: black !important;
	}

	.b-editable {
		box-sizing: border-box !important;
		border: 2px solid transparent !important;
	}
</style>

<iframe frameBorder="0" style="width: 100%; overflow: hidden; z-index: 10000; height: 0; background: transparent; position: fixed;<? if (Editor::instance()->state() !== Editor::EDIT): ?>border: none; width: 100px; right: 0; <? endif; ?>" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>