<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>
	<div id="child-settings" class="boom-tabs">
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
				<label for="children_template_id"><?=__('Default child template')?></label>
				<?= Form::select('children_template_id', $templates, $default_child_template, array('id' => 'children_template_id')) ?>
			</p>

			<p>
				<label for="child_template_cascade"><?= __('Update existing child pages') ?></label>
				<?= Form::checkbox('cascade_template', '1', false, array('id' => 'child_template_cascade')); ?>
			</p>

			<p>
				<label for="children_ordering_policy"><?=__('Child ordering policy')?></label>

				<?= Form::select('children_ordering_policy', array(
						'sequence'		=>	'Manual',
						'visible_from'	=>	'Date',
						'title'			=>	'Alphabetic'
					), $child_order_column, array('id' => 'children_ordering_policy'));
				?>
				<?= Form::select('children_ordering_direction', array(
						'asc'		=>	'Ascending',
						'desc'	=>	'Descending'
					), $child_order_direction);
				?>

				<a href="#" id="b-page-settings-children-reorder"<? if ($child_order_column != 'sequence'): ?> class="ui-helper-hidden"<? endif ?>>Reorder</a>
			</p>
		</div>
		<? if ($allowAdvanced): ?>
			<div id="advanced">
				<p>
					<label for="children_visible_in_nav"><?=__('Children visible in nav')?>?</label>

					<?= Form::select('children_visible_in_nav', array(
							1 => 'Yes',
							0 => 'No',
						), $page->children_visible_in_nav, array('id' => 'children_visible_in_nav'));
					?>
				</p>

				<p>
					<label for="visible_in_nav_cascade"><?= __('Update existing child pages') ?></label>
					<?= Form::checkbox('cascade[]', 'visible_in_nav', false, array('id' => 'visible_in_nav_cascade')); ?>
				</p>

				<p>
					<label for="children_visible_in_nav_cms"><?=__('Children visible in CMS nav')?>?</label>

						<?= Form::select('children_visible_in_nav_cms', array(
								1 => 'Yes',
								0 => 'No',
							), $page->children_visible_in_nav_cms, array('id' => 'children_visible_in_nav_cms')); ?>
				</p>

				<p>
					<label for="visible_in_nav_cms_cascade"><?= __('Update existing child pages') ?></label>
					<?= Form::checkbox('cascade[]', 'visible_in_nav_cms', false, array('id' => 'visible_in_nav_cms_cascade')); ?>
				</p>

				<p>
					<label for="children_url_prefix"><?=__('Default child URI prefix')?></label>
					<?= Form::input('children_url_prefix', $page->children_url_prefix, array('id' => 'children_url_prefix')); ?>
				</p>

				<p>
					<label for="grandchild_template_id"><?=__('Default grandchild template')?></label>
					<?= Form::select('grandchild_template_id', $templates, $default_grandchild_template, array('id' => 'grandchild_template_id')) ?>
				</p>
			</div>
		<? endif; ?>
	</div>
</form>