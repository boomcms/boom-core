<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>

	<div id="b-pagesettings-search" class="boom-tabs">
		<? if ($allow_advanced): ?>
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
			<p>
				<label for="description"><?= __('Description') ?></label>
				<textarea id="description" name="description" rows="5"><?= $page->description() ?></textarea>
			</p>

			<p>
				<label for="keywords"><?= __('Keywords') ?></label>
				<textarea name="keywords" id="keywords" rows="5"><?=$page->keywords ?></textarea>
			</p>
		</div>

		<? if ($allow_advanced): ?>
			<div id="advanced">
				<p>
					<label for="external_indexing"><?= __('Allow indexing by search engines') ?></label>
					<?= Form::select('external_indexing', array(1 => 'Yes', 0 => 'No'), $page->external_indexing, array('id' => 'external_indexing')) ?>
				</p>

				<p>
					<label for="internal_indexing"><?= __('Show in site search results') ?></label>
					<?= Form::select('internal_indexing', array(1 => 'Yes', 0 => 'No'), $page->internal_indexing, array('id' => 'internal_indexing')) ?>
				</p>
			</div>
		<? endif; ?>
	</div>
</form>