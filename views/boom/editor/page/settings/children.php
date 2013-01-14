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
			<label for="children_template_id"><?=__('Default child template')?></label>
			
			<?= Form::select('children_template_id', $templates, $default_child_template) ?>

			<label for="child_template_cascade">Update existing child pages</label>
			<?= Form::checkbox('child_template_cascade', '1', false, array('id' => 'child_template_cascade')); ?>
			
			<label for="child_ordering_policy"><?=__('Child ordering policy')?></label>
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
		</div>
		<? if ($allow_advanced): ?>
			<div id="child-settings-advanced">
				
				<label for="children_visible_in_nav"><?=__('Children visible in nav')?>?</label>
				<?= Form::select('children_visible_in_nav', array(
							1	=>	'Yes',
							0	=>	'No',
						), $page->children_visible_in_nav); ?>

				<label for="visible_in_nav_cascade">Update existing child pages</label>
				<?= Form::checkbox('visible_in_nav_cascade', '1', false, array('id' => 'visible_in_nav_cascade')); ?>
				
				<label for="vhildren_visible_in_nav_cms"><?=__('Children visible in CMS nav')?>?</label>
				
				<?= Form::select('children_visible_in_nav_cms', array(
							1	=>	'Yes',
							0	=>	'No',
						), $page->children_visible_in_nav_cms); ?>

				<label for="visible_in_nav_cms_cascade">Update existing child pages</label>
				<?= Form::checkbox('visible_in_nav_cms_cascade', 1, false, array('id' => 'visible_in_nav_cms_cascade')); ?>
				
				<label for="children_link_prefix"><?=__('Default child URI prefix')?></label>
				
				<?= Form::input('children_link_prefix', $page->children_link_prefix); ?>
				
				<label for="grandchild_template_id"><?=__('Default grandchild template')?></label>
				
				<?= Form::select('grandchild_template_id', $templates, $default_grandchild_template) ?>
			</div>
		<? endif; ?>
	</div>
</form>