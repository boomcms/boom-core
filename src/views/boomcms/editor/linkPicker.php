<div id="b-linkpicker-container">
	<div class="boom-tabs b-linkpicker">
		<ul>
			<li class="toggle-internal">
                <a href="#b-linkpicker-add-internal">
                    <?= trans('boomcms::editor.link-picker.tabs.internal') ?>
                </a>
            </li>
			
            <li class="toggle-external">
                <a href="#b-linkpicker-add-external">
                    <?= trans('boomcms::editor.link-picker.tabs.external') ?>
                </a>
            </li>

            <li class="toggle-asset">
                <a href="#b-linkpicker-add-asset">
                    <?= trans('boomcms::editor.link-picker.tabs.asset') ?>
                </a>
            </li>

            <li class="toggle-text">
                <a href="#b-linkpicker-text">
                    <?= trans('boomcms::editor.link-picker.tabs.text') ?>
                </a>
            </li>
		</ul>

		<div id="b-linkpicker-add-internal" class="toggle-internal">
			<p><?= trans('boomcms::editor.link-picker.internal') ?></p>
			<ul class="boom-tree"></ul>
		</div>

		<div id="b-linkpicker-add-external" class="toggle-external">
			<p>
				<?= trans('boomcms::editor.link-picker.external') ?>
			</p>

			<form action="#">
				<select>
					<option value="http" selected="selected">Website</option>
					<option value="mailto">Email</option>
					<option value="tel">Phone number</option>
				</select>

				<input type="text" />
			</form>
		</div>

		<div id="b-linkpicker-add-asset" class="toggle-asset">
            <label>
                <p><?= trans('boomcms::editor.link-picker.asset-action') ?></p>
                
                <select>
                    <option value="view" selected>
                        <?= trans('boomcms::editor.link-picker.action-view') ?>
                    </option>

                    <option value="download">
                        <?= trans('boomcms::editor.link-picker.action-download') ?>
                    </option>
                </select>
            </label>

			<?= $button('paperclip', 'select-asset', [
                'id' => 'b-linkpicker-asset-select',
                'class' => 'b-button-withtext'
            ]) ?>
		
            <img />
        </div>

		<div id="b-linkpicker-text" class="toggle-text">
			<p>
                <?= trans('boomcms::editor.link-picker.text') ?>
			</p>

			<form action="#">
				<input type="text" name="link_text" />
			</form>
		</div>
	</div>

    <div class="buttons">
        <?= $button('trash-o', 'remove-link', [
            'id' => 'b-linkpicker-remove',
            'class' => 'b-button-withtext toggle-remove'
        ]) ?>
    </div>
</div>
