<form>
	<div id="child-settings">
        <h1><?= Lang::get('boom::settings.children.heading') ?></h1>

		<section id="basic">
            <h2><?= Lang::get('boom::settings.basic') ?></h2>

            <label>
                <p><?= Lang::get('boom::settings.children.template') ?></p>

                <select name="children_template_id" id="children_template_id">
                    <?php foreach ($templates as $t): ?>
                      <option value="<?= $t->getId() ?>"<?php if ($t->getId() === $default_child_template): ?> selected<?php endif ?>><?= $t->getName() ?></option>
                    <?php endforeach ?>
                </select>
            </label>

            <label>
                <p><?= Lang::get('boom::settings.children.order') ?></p>

                <select name="children_ordering_policy" id="children_ordering_policy">
                    <option value="sequence"<?php if ($child_order_column === 'sequence'): ?> selected="selected"<?php endif ?>>Manual</option>
                    <option value="visible_from"<?php if ($child_order_column === 'visible_from'): ?> selected="selected"<?php endif ?>>Date by visible from time</option>
                    <option value="title"<?php if ($child_order_column === 'title'): ?> selected="selected"<?php endif ?>>Alphabetic by title</option>
                </select>

                <select name="children_ordering_direction">
                    <option value="asc"<?php if ($child_order_direction === 'asc'): ?> selected="selected"<?php endif ?>>Ascending</option>
                    <option value="desc"<?php if ($child_order_direction === 'desc'): ?> selected="selected"<?php endif ?>>Descending</option>
                </select>

                <?php if ($page->hasChildren()): ?>
                    <?= $button('', Lang::get('boom::buttons.reorder'), ['id' => 'b-page-settings-children-reorder', 'class' => 'b-button-textonly']) ?>
                <?php endif ?>
            </label>
		</section>

		<?php if ($allowAdvanced): ?>
			<section id="advanced">
                <h2><?= Lang::get('boom::settings.advanced') ?></h2>

                <label>
                    <p><?= Lang::get('boom::settings.children.nav') ?></p>

                    <select name="children_visible_in_nav" id="children_visible_in_nav">
                        <option value="1"<?php if ($page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if (!$page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <p><?= Lang::get('boom::settings.children.nav-cms') ?></p>

                    <select name="children_visible_in_nav_cms" id="children_visible_in_nav_cms">
                        <option value="1"<?php if ($page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if (!$page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <p><?= Lang::get('boom::settings.children.uri-prefix') ?></p>
                    <input type="text" id="children_url_prefix" name="children_url_prefix" value="<?= $page->getChildPageUrlPrefix() ?>" />
                </label>

                <label>
                    <p><?= Lang::get('boom::settings.children.grandchild-template') ?></p>

                    <select name="grandchild_template_id" id="grandchild_template_id">
                        <?php foreach ($templates as $t): ?>
                            <option value="<?= $t->getId() ?>"<?php if ($t->getId() === $default_grandchild_template): ?> selected<?php endif ?>><?= $t->getName() ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
			</section>
		<?php endif ?>
	</div>

    <?= $button('refresh', Lang::get('boom::buttons.reset'), ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', Lang::get('boom::buttons.save'), ['class' => 'b-button-save b-button-withtext']) ?>
</form>
