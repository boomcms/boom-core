<form class="b-form-settings">
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
                    <label>
                        <?=__('Default child template')?>
                        <?= Form::select('children_template_id', $templates, $default_child_template, array('id' => 'children_template_id')) ?>
                    </label>

                    <?/*label>
                        <?= __('Update existing child pages') ?>
                        <?= Form::checkbox('cascade_template', '1', false, array('id' => 'child_template_cascade')) ?>
                    </label*/?>
				
                    <label>
                        <?=__('Child ordering policy')?>
    
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
                    </label>
		</div>
		<? if ($allowAdvanced): ?>
			<div id="advanced">
                            <label>
                                <?=__('Children visible in nav')?>?
    
                                <?= Form::select('children_visible_in_nav', array(
                                        1 => 'Yes',
                                        0 => 'No',
                                    ), $page->childrenAreVisibleInNav(), array('id' => 'children_visible_in_nav'));
                                ?>
                            </label>

                            <?/*label>
                                <?= __('Update existing child pages') ?>
                                <?= Form::checkbox('cascade[]', 'visible_in_nav', false, array('id' => 'visible_in_nav_cascade')) ?>
                            </label*/?>
					
                            <label>
                                <?=__('Children visible in CMS nav')?>?
                                <?= Form::select('children_visible_in_nav_cms', array(
                                        1 => 'Yes',
                                        0 => 'No',
                                    ), $page->childrenAreVisibleInCmsNav(), array('id' => 'children_visible_in_nav_cms')) ?>
                            </label>
					
                            <?/*label>
                                <?= __('Update existing child pages') ?>
                                <?= Form::checkbox('cascade[]', 'visible_in_nav_cms', false, array('id' => 'visible_in_nav_cms_cascade')) ?>
                            </label*/?>
                
                            <label>
                                <?=__('Default child URI prefix')?>
                                <?= Form::input('children_url_prefix', $page->getChildPageUrlPrefix(), array('id' => 'children_url_prefix')) ?>
                            </label>
			
                            <label>
                                <?=__('Default grandchild template')?>
                                <?= Form::select('grandchild_template_id', $templates, $default_grandchild_template, array('id' => 'grandchild_template_id')) ?>
                            </label>
			</div>
		<? endif ?>
	</div>
</form>