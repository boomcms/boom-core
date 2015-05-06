<form class="b-form-settings">
	<div id="child-settings" class="boom-tabs">
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
                        <?=Lang::get('Default child template')?>

                        <select name="children_template_id" id="children_template_id">
                            <?php foreach($templates as $t): ?>
                              <option value="<?= $t->getId() ?>"<?php if($t->getId() === $default_child_template): ?> selected<?php endif ?>><?= $t->getName() ?></option>
                            <?php endforeach ?>
                        </select>
                    </label>

                    <?/*label>
                        <?= Lang::get('Update existing child pages') ?>
                        <?= Form::checkbox('cascade_template', '1', false, array('id' => 'child_template_cascade')) ?>
                    </label*/?>

                    <label>
                        <?=Lang::get('Child ordering policy')?>

                        <?= Form::select('children_ordering_policy', array(
                                'sequence'        =>    'Manual',
                                'visible_from'    =>    'Date',
                                'title'            =>    'Alphabetic'
                            ), $child_order_column, array('id' => 'children_ordering_policy'));
                        ?>
                        <?= Form::select('children_ordering_direction', array(
                                'asc'        =>    'Ascending',
                                'desc'    =>    'Descending'
                            ), $child_order_direction);
                        ?>

                        <?= new Boom\UI\Button('', 'Re-order', array('id' => 'b-page-settings-children-reorder', 'class' => 'b-button-textonly')) ?>
                    </label>
		</div>
		<?php if ($allowAdvanced): ?>
			<div id="advanced">
                            <label>
                                <?=Lang::get('Children visible in nav')?>?

                                <?= Form::select('children_visible_in_nav', array(
                                        1 => 'Yes',
                                        0 => 'No',
                                    ), (int) $page->childrenAreVisibleInNav(), array('id' => 'children_visible_in_nav'));
                                ?>
                            </label>

                            <?/*label>
                                <?= Lang::get('Update existing child pages') ?>
                                <?= Form::checkbox('cascade[]', 'visible_in_nav', false, array('id' => 'visible_in_nav_cascade')) ?>
                            </label*/?>

                            <label>
                                <?=Lang::get('Children visible in CMS nav')?>?
                                <?= Form::select('children_visible_in_nav_cms', array(
                                        1 => 'Yes',
                                        0 => 'No',
                                    ), (int) $page->childrenAreVisibleInCmsNav(), array('id' => 'children_visible_in_nav_cms')) ?>
                            </label>

                            <?/*label>
                                <?= Lang::get('Update existing child pages') ?>
                                <?= Form::checkbox('cascade[]', 'visible_in_nav_cms', false, array('id' => 'visible_in_nav_cms_cascade')) ?>
                            </label*/?>

                            <label>
                                <?=Lang::get('Default child URI prefix')?>
                                <?= Form::input('children_url_prefix', $page->getChildPageUrlPrefix(), array('id' => 'children_url_prefix')) ?>
                            </label>

                            <label>
                                <?=Lang::get('Default grandchild template')?>

                                <select name="grandchild_template_id" id="grandchild_template_id">
                                    <?php foreach($templates as $t): ?>
                                        <option value="<?= $t->getId() ?>"<?php if($t->getId() === $default_grandchild_template): ?> selected<?php endif ?>><?= $t->getName() ?></option>
                                    <?php endforeach ?>
                                </select>
                            </label>
			</div>
		<?php endif ?>
	</div>
</form>
