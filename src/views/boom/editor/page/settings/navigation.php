<form class="b-form-settings">
	<div class="boom-tabs">
		<?php if ($allowAdvanced): ?>
			<ul>
				<li>
					<a href="#basic"><?= Lang::get('Basic') ?></a>
				</li>
				<li>
					<a href="#advanced"><?= Lang::get('Advanced') ?></a>
				</li>
			</ul>
		<?php endif ?>

		<div id="basic">
                    <label>
                        <?= Lang::get('Visible in navigation') ?>?

                        <select name="visible_in_nav" id="visible_in_nav">
                            <option value="1"<?php if ($page->isVisibleInNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                            <option value="0"<?php if ( ! $page->isVisibleInNav()): ?> selected="selected"<?php endif ?>>No</option>
                        </select>
                    </label>

                    <label>
                        <?= Lang::get('Visible in CMS navigation') ?>?

                        <select name="visible_in_nav_cms" id="visible_in_nav_cms">
                            <option value="1"<?php if ($page->isVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                            <option value="0"<?php if ( ! $page->isVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>No</option>
                        </select>
                    </label>
		</div>

		<?php if ($allowAdvanced): ?>
			<div id='advanced'>
				<label for="parent_id">Parent page</label>

				<input type="hidden" name="parent_id" value="<?= $page->getParentId() ?>">
				<ul class="boom-tree"></ul>
			</div>
		<?php endif ?>
	</div>
</form>
