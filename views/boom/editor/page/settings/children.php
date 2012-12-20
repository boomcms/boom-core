<form id="boom-form-pagesettings-childsettings" name="pagesettings-childsettings">
	<div id="child-settings" class="boom-tabs s-pagesettings">
		<ul>
			<li>
				<a href="#child-settings-basic"><?=__('Basic')?></a>
			</li>
			<? if ($allow_advanced): ?>
				<li>
					<a href="#child-settings-advanced"><?=__('Advanced')?></a>
				</li>
			<? endif; ?>
		</ul>

		<div id="child-settings-basic">
			<table width="100%">
				<tr>
					<td><?=__('Default child template')?></td>
					<td>
						<?= Form::select('children_template_id', $templates, $default_child_template) ?>

						<label for="child_template_cascade">Update existing child pages</label>
						<?= Form::checkbox('child_template_cascade', '1', false, array('id' => 'child_template_cascade')); ?>
					</td>
				</tr>
				<tr>
					<td><?=__('Child ordering policy')?></td>
					<td>
						<?= Form::select('child_ordering_policy', array(
								'sequence'		=>	'Manual',
								'visible_from'	=>	'Date',
								'title'			=>	'Alphabetic'
							), $child_order_column);
						?>
						<?= Form::select('child_ordering_direction', array(
								'asc'		=>	'Ascending',
								'desc'	=>	'Descending'
							), $child_order_direction);
						?>
					</td>
				</tr>
			</table>
		</div>
		<? if ($allow_advanced): ?>
			<div id="child-settings-advanced">
				<table width="100%">
					<tr>
						<td><?=__('Children visible in nav')?>?</td>
						<td>
							<?= Form::select('children_visible_in_nav', array(
										1	=>	'Yes',
										0	=>	'No',
									), $page->children_visible_in_nav); ?>

							<label for="visible_in_nav_cascade">Update existing child pages</label>
							<?= Form::checkbox('visible_in_nav_cascade', '1', false, array('id' => 'visible_in_nav_cascade')); ?>
						</td>
					</tr>
					<tr>
						<td><?=__('Children visible in CMS nav')?>?</td>
						<td>
							<?= Form::select('children_visible_in_nav_cms', array(
										1	=>	'Yes',
										0	=>	'No',
									), $page->children_visible_in_nav_cms); ?>

							<label for="visible_in_nav_cms_cascade">Update existing child pages</label>
							<?= Form::checkbox('visible_in_nav_cms_cascade', 1, false, array('id' => 'visible_in_nav_cms_cascade')); ?>
						</td>
					</tr>
					<tr>
						<td><?=__('Default child URI prefix')?></td>
						<td>
							<?= Form::input('children_link_prefix', $page->children_link_prefix); ?>
						</td>
					</tr>
					<tr>
						<td><?=__('Default grandchild template')?></td>
						<td>
							<?= Form::select('grandchild_template_id', $templates, $default_grandchild_template) ?>
						</td>
					</tr>
				</table>
			</div>
		<? endif; ?>
	</div>
</form>