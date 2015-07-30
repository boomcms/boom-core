<form class="b-form-settings">
	<div id="child-settings" class="boom-tabs">
		<?php if ($allowAdvanced): ?>
			<ul>
				<li>
					<a href="#basic"><?= Lang::get('Basic') ?></a>
				</li>
				<li>
					<a href="#advanced"><?= Lang::get('Advanced') ?></a>
				</li>
			</ul>
		<?php endif ?>

		<div id="basic">
                    <label>
                        <?= Lang::get('Default child template') ?>

                        <select name="children_template_id" id="children_template_id">
                            <?php foreach($templates as $t): ?>
                              <option value="<?= $t->getId() ?>"<?php if($t->getId() === $default_child_template): ?> selected<?php endif ?>><?= $t->getName() ?></option>
                            <?php endforeach ?>
                        </select>
                    </label>

                    <label>
                        <?= Lang::get('Child ordering policy') ?>

                        <select name="children_ordering_policy" id="children_ordering_policy">
                            <option value="sequence"<?php if ($child_order_column === 'sequence'): ?> selected="selected"<?php endif ?>>Manual</option>
                            <option value="visible_from"<?php if ($child_order_column === 'visible_from'): ?> selected="selected"<?php endif ?>>Date by visible from time</option>
                            <option value="title"<?php if ($child_order_column === 'title'): ?> selected="selected"<?php endif ?>>Alphabetic by title</option>
                        </select>

                        <select name="children_ordering_direction">
                            <option value="asc"<?php if ($child_order_direction === 'asc'): ?> selected="selected"<?php endif ?>>Ascending</option>
                            <option value="desc"<?php if ($child_order_direction === 'desc'): ?> selected="selected"<?php endif ?>>Descending</option>
                        </select>

                        <?= new BoomCMS\UI\Button('', 'Re-order', ['id' => 'b-page-settings-children-reorder', 'class' => 'b-button-textonly']) ?>
                    </label>
		</div>
		<?php if ($allowAdvanced): ?>
			<div id="advanced">
                <label>
                    <?= Lang::get('Children visible in nav') ?>?

                    <select name="children_visible_in_nav" id="children_visible_in_nav">
                        <option value="1"<?php if ($page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if ( ! $page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <?= Lang::get('Children visible in CMS nav') ?>?

                    <select name="children_visible_in_nav_cms" id="children_visible_in_nav_cms">
                        <option value="1"<?php if ($page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if ( ! $page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <?= Lang::get('Default child URI prefix') ?>
                    <input type="text" id="children_url_prefix" name="children_url_prefix" value="<?= $page->getChildPageUrlPrefix() ?>" />
                </label>

                <label>
                    <?= Lang::get('Default grandchild template') ?>

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
