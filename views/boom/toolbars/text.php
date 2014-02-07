<div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">
	<div class="commands">
		<?= BoomUI::button('accept', __('Accept changes'), array('data-wysihtml5-action' => '', 'class' => 'action', 'id' => 'b-editor-accept')) ?>
		<?= BoomUI::button('cancel', __('Discard changes'), array('data-wysihtml5-action' => '', 'class' => 'action', 'id' => 'b-editor-cancel')) ?>

		<? if ($mode == 'inline' OR $mode == 'block'): ?>
			<?= BoomUI::button('bold', __('Make text bold (CTRL + B)'), array('data-wysihtml5-command' => 'bold', 'class' => 'action')) ?>
			<?= BoomUI::button('italic', __('Make text italic (CTRL + I)'), array('data-wysihtml5-command' => 'italic', 'class' => 'action')) ?>

			<? if ($mode == 'block'): ?>
				<?= BoomUI::button('list', __('Insert an unordered list'), array('data-wysihtml5-command' => 'insertUnorderedList', 'class' => 'command')) ?>
				<?= BoomUI::button('ol', __('Insert an ordered list'), array('data-wysihtml5-command' => 'insertOrderedList', 'class' => 'command')) ?>
				<?= BoomUI::button('hr', __('Insert horizontal rule'), array('data-wysihtml5-command' => 'insertHorizontalRule', 'class' => 'command')) ?>
			<? endif ?>

			<?= BoomUI::button('link', __('Insert a link'), array('data-wysihtml5-command' => 'createLink', 'class' => 'command')) ?>

			<? if ($mode == 'block'): ?>
				<?= BoomUI::button('asset', __('Insert an asset'), array('data-wysihtml5-command' => 'insertBoomAsset', 'class' => 'command')) ?>
				<?= BoomUI::button('paragraph', __('Insert paragraph'), array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'p', 'class' => 'command')) ?>
				<?= BoomUI::button('h2', __('Insert headline 2'), array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h2', 'class' => 'command')) ?>
				<?= BoomUI::button('h3', __('Insert headline 3'), array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h3', 'class' => 'command')) ?>
				<?= BoomUI::button('blockquote', __('Insert blockquote'), array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'blockquote', 'class' => 'command')) ?>
			<? endif ?>
		<? endif ?>
	</div>

	<div data-wysihtml5-dialog="createLink" style="display: none">
		<label>
			Link:
			<input data-wysihtml5-dialog-field="href" value="http://" disabled="disabled">
		</label>

		<a href="#" id="b-editor-link">edit</a>
	</div>
</div>