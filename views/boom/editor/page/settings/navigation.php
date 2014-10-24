<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>

	<div class="boom-tabs">
		<? if ($allowAdvanced): ?>
			<ul>
				<li>
					<a href="#basic"><?=__('Basic')?></a>
				</li>
				<li>
					<a href="#advanced"><?=__('Advanced')?></a>
				</li>
			</ul>
		<? endif; ?>

		<div id="basic">
			<p>
				<label for="visible_in_nav"><?= __('Visible in navigation') ?>?</label>
				<?= Form::select('visible_in_nav', array(1 => 'Yes', 0 => 'No'), $page->isVisibleInNav(), array('id' => 'visible_in_nav')) ?>
			</p>

			<p>
				<label for="visible_in_nav_cms"><?= __('Visible in CMS navigation') ?>?</label>
				<?= Form::select('visible_in_nav_cms', array(1 => 'Yes', 0 => 'No'), $page->isVisibleInCmsNav(), array('id' => 'visible_in_nav_cms')) ?>
			</p>
		</div>

		<? if ($allowAdvanced): ?>
			<div id='advanced'>
				<label for="parent_id">Parent page</label>

				<input type="hidden" name="parent_id" value="<?= $page->parent()->getId() ?>">
				<ul class="boom-tree">
					<li><a id="page_5" href="/" rel="5">Home</a></li>
				</ul>
			</div>
		<? endif; ?>
	</div>
</form>