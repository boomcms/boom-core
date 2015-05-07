<form class="b-form-settings">
	<div id="b-pagesettings-search" class="boom-tabs">
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
                        <?= Lang::get('Description') ?>
                        <textarea name="description" rows="5"><?= $page->getDescription() ?></textarea>
                    </label>

                    <label>
                        <?= Lang::get('Keywords') ?>
                        <textarea name="keywords" rows="5"><?=$page->getKeywords() ?></textarea>
                    </label>
		</div>

		<?php if ($allowAdvanced): ?>
			<div id="advanced">
                            <label>
                                <?= Lang::get('Allow indexing by search engines') ?>
                                <select name="external_indexing" id="external_indexing">
                                    <option value="1"<?php if ($page->allowsExternalIndexing()): ?> selected="selected"<?php endif ?>>Yes</option>
                                    <option value="0"<?php if ( ! $page->allowsExternalIndexing()): ?> selected="selected"<?php endif ?>>No</option>
                                </select>
                            </label>

                            <label>
                                <?= Lang::get('Show in site search results') ?>
                                <select name="internal_indexing" id="internal_indexing">
                                    <option value="1"<?php if ($page->allowsInternalIndexing()): ?> selected="selected"<?php endif ?>>Yes</option>
                                    <option value="0"<?php if ( ! $page->allowsInternalIndexing()): ?> selected="selected"<?php endif ?>>No</option>
                                </select>
                            </label>
			</div>
		<?php endif ?>
	</div>
</form>
