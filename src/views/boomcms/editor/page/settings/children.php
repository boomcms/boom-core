<form>
	<div id="child-settings">
        <h1><?= trans('boomcms::settings.children.heading') ?></h1>

		<section id="basic">
            <h2><?= trans('boomcms::settings.basic') ?></h2>

            <label>
                <p><?= trans('boomcms::settings.children.template') ?></p>

                <select name="children_template_id" id="children_template_id" data-default="<?= $childTemplate ?>">
                    <?php foreach ($templates as $template): ?>
                        <option value="<?= $template->getId() ?>"
                            <?php if ($template->getId() === $childTemplate): ?> selected<?php endif ?>
                        >
                            <?= $template->getName() ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </label>

            <label>
                <p><?= trans('boomcms::settings.children.order') ?></p>

                <select name="children_ordering_policy" id="children_ordering_policy">
                    <?php foreach (['sequence', 'visible_from', 'title'] as $order): ?>
                        <option value="<?= $order ?>"
                            <?php if ($orderColumn === $order): ?> selected="selected"<?php endif ?>
                        >
                            <?= trans("boomcms::settings.children.column.$order") ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <select name="children_ordering_direction">
                    <?php foreach (['asc', 'desc'] as $direction): ?>
                        <option value="<?= $direction ?>"
                            <?php if ($orderDirection === $direction): ?> selected="selected"<?php endif ?>
                        >
                            <?= trans("boomcms::settings.children.direction.$direction") ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <?php if ($page->hasChildren()): ?>
                    <?= $button('', 'reorder', ['id' => 'b-page-settings-children-reorder', 'class' => 'b-button-textonly']) ?>
                <?php endif ?>
            </label>
		</section>

		<?php if (Gate::allows('editChildrenAdvanced', $page)): ?>
			<section id="advanced">
                <h2><?= trans('boomcms::settings.advanced') ?></h2>

                <label>
                    <p><?= trans('boomcms::settings.children.nav') ?></p>

                    <select name="children_visible_in_nav" id="children_visible_in_nav">
                        <option value="1"<?php if ($page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value=""<?php if (!$page->childrenAreVisibleInNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <p><?= trans('boomcms::settings.children.nav-cms') ?></p>

                    <select name="children_visible_in_nav_cms" id="children_visible_in_nav_cms">
                        <option value="1"<?php if ($page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value=""<?php if (!$page->childrenAreVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <p><?= trans('boomcms::settings.children.uri-prefix') ?></p>
                    <input type="text" id="children_url_prefix" name="children_url_prefix" value="<?= $page->getChildPageUrlPrefix() ?>" />
                </label>

                <label>
                    <p><?= trans('boomcms::settings.children.grandchild-template') ?></p>

                    <select name="grandchild_template_id" id="grandchild_template_id">
                        <?php foreach ($templates as $t): ?>
                            <option value="<?= $t->getId() ?>"
                                <?php if ($t->getId() === $grandchildTemplate): ?> selected<?php endif ?>
                            ><?= $t->getName() ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
			</section>
		<?php endif ?>
	</div>

    <?= $button('refresh', 'reset', ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', 'save', ['class' => 'b-button-save b-button-withtext']) ?>
</form>
