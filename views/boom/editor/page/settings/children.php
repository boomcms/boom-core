<form id="boom-form-pagesettings-childsettings" name="pagesettings-childsettings">
	<div id="child-settings" class="boom-tabs s-pagesettings">
		<? if ($allow_advanced): ?>
			<ul>
				<li>
					<a href="#child-settings-basic"><?=__('Basic')?></a>
				</li>
				<li>
					<a href="#child-settings-advanced"><?=__('Advanced')?></a>
				</li>
			</ul>
		<? endif; ?>

		<div id="child-settings-basic">
			<label for="children_template_id"><?=__('Default child template')?>

			<?= Form::select('children_template_id', $templates, $default_child_template) ?>
			</label>

			<label for="child_template_cascade">Update existing child pages
			<?= Form::checkbox('cascade[]', 'template_id', false, array('id' => 'child_template_cascade')); ?>
			</label>

			<label for="children_ordering_policy"><?=__('Child ordering policy')?>
			<?= Form::select('children_ordering_policy', array(
					'sequence'		=>	'Manual',
					'visible_from'	=>	'Date',
					'title'			=>	'Alphabetic'
				), $child_order_column);
			?>
			<?= Form::select('children_ordering_direction', array(
					'asc'		=>	'Ascending',
					'desc'	=>	'Descending'
				), $child_order_direction);
			?>
			</label>
		</div>
		<? if ($allow_advanced): ?>
			<div id="child-settings-advanced">

				<label for="children_visible_in_nav"><?=__('Children visible in nav')?>?
				<?= Form::select('children_visible_in_nav', array(
							1	=>	'Yes',
							0	=>	'No',
						), $page->children_visible_in_nav); ?>
				</label>

				<label for="visible_in_nav_cascade">Update existing child pages
				<?= Form::checkbox('cascade[]', 'visible_in_nav', false, array('id' => 'visible_in_nav_cascade')); ?>
				</label>

				<label for="children_visible_in_nav_cms"><?=__('Children visible in CMS nav')?>?

				<?= Form::select('children_visible_in_nav_cms', array(
							1	=>	'Yes',
							0	=>	'No',
						), $page->children_visible_in_nav_cms); ?>
				</label>

				<label for="visible_in_nav_cms_cascade">Update existing child pages
				<?= Form::checkbox('cascade', 'visible_in_nav_cms', false, array('id' => 'visible_in_nav_cms_cascade')); ?>
				</label>

				<label for="children_url_prefix"><?=__('Default child URI prefix')?>

				<?= Form::input('children_url_prefix', $page->children_url_prefix); ?>
				</label>

				<label for="grandchild_template_id"><?=__('Default grandchild template')?>

				<?= Form::select('grandchild_template_id', $templates, $default_grandchild_template) ?>
				</label>
			</div>
		<? endif; ?>
	</div>
</form>