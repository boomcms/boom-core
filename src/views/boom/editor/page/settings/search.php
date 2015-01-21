<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>

	<div id="b-pagesettings-search" class="boom-tabs">
		<? if ($allowAdvanced): ?>
			<ul>
				<li>
					<a href="#basic"><?= __('Basic') ?></a>
				</li>
				<li>
					<a href="#advanced"><?= __('Advanced') ?></a>
				</li>
			</ul>
		<? endif; ?>

		<div id="basic">
                    <label>
                        <?= __('Description') ?>
                        <textarea name="description" rows="5"><?= $page->getDescription() ?></textarea>
                    </label>
			
                    <label>
                        <?= __('Keywords') ?>
                        <textarea name="keywords" rows="5"><?=$page->getKeywords() ?></textarea>
                    </label>
		</div>

		<? if ($allowAdvanced): ?>
			<div id="advanced">
                            <label>
                                <?= __('Allow indexing by search engines') ?>
                                <?= Form::select('external_indexing', array(1 => 'Yes', 0 => 'No'), (int) $page->allowsExternalIndexing(), array('id' => 'external_indexing')) ?>
                            </label>
							
                            <label>
                                <?= __('Show in site search results') ?>
                                <?= Form::select('internal_indexing', array(1 => 'Yes', 0 => 'No'), (int) $page->allowsInternalIndexing(), array('id' => 'internal_indexing')) ?>
                            </label>
			</div>
		<? endif; ?>
	</div>
</form>