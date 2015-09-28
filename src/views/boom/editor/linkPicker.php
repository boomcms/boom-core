<div id="b-linkpicker-container">
	<div class="boom-tabs b-linkpicker">
		<ul>
			<li><a href="#b-linkpicker-add-internal"><?= Lang::get('boom::editor.link-picker.tabs.internal') ?></a></li>
			<li><a href="#b-linkpicker-add-external"><?= Lang::get('boom::editor.link-picker.tabs.external') ?></a></li>
			<li><a href="#b-linkpicker-add-asset"><?= Lang::get('boom::editor.link-picker.tabs.asset') ?></a></li>
			<li><a href="#b-linkpicker-text"><?= Lang::get('boom::editor.link-picker.tabs.text') ?></a></li>
		</ul>

		<div id="b-linkpicker-add-internal">
			<p><?= Lang::get('boom::editor.link-picker.internal') ?></p>
			<ul class="boom-tree"></ul>
		</div>

		<div id="b-linkpicker-add-external">
			<p>
				<?= Lang::get('boom::editor.link-picker.external') ?>
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

		<div id="b-linkpicker-add-asset">
            <label>
                <p><?= Lang::get('boom::editor.link-picker.asset-action') ?></p>
                
                <select>
                    <option value="view" selected><?= Lang::get('boom::editor.link-picker.action-view') ?></option>
                    <option value="download"><?= Lang::get('boom::editor.link-picker.action-download') ?></option>
                </select>
            </label>

			<?= $button('paperclip', 'select-asset', ['id' => 'b-linkpicker-asset-select', 'class' => 'b-button-withtext']) ?>
		
            <img />
        </div>

		<div id="b-linkpicker-text">
			<p>
                <?= Lang::get('boom::editor.link-picker.text') ?>
			</p>

			<form action="#">
				<input type="text" name="link_text" />
			</form>
		</div>
	</div>

    <?= $button('trash-o', 'remove-link', ['id' => 'b-linkpicker-remove', 'class' => 'b-button-withtext']) ?>
</div>
