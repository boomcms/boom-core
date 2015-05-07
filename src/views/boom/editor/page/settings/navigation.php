<form class="b-form-settings">
	<div class="boom-tabs">
		<?php if ($allowAdvanced): ?>
			<ul>
				<li>
					<a href="#basic"><?=Lang::get('Basic')?></a>
				</li>
				<li>
					<a href="#advanced"><?=Lang::get('Advanced')?></a>
				</li>
			</ul>
		<?php endif; ?>

		<div id="basic">
                    <label>
                        <?= Lang::get('Visible in navigation') ?>?
                        <?= Form::select('visible_in_nav', [1 => 'Yes', 0 => 'No'], $page->isVisibleInNav(), ['id' => 'visible_in_nav']) ?>
                    </label>

                    <label>
                        <?= Lang::get('Visible in CMS navigation') ?>?
                        <?= Form::select('visible_in_nav_cms', [1 => 'Yes', 0 => 'No'], $page->isVisibleInCmsNav(), ['id' => 'visible_in_nav_cms']) ?>
                    </label>
		</div>

		<?php if ($allowAdvanced): ?>
			<div id='advanced'>
				<label for="parent_id">Parent page</label>

				<input type="hidden" name="parent_id" value="<?= $page->parent()->getId() ?>">
				<ul class="boom-tree">
					<li><a id="page_5" href="/" rel="5">Home</a></li>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</form>
