<form>
    <h1><?= Lang::get('boom::settings.navigation.heading') ?></h1>

    <section id="basic">
        <h2><?= Lang::get('boom::settings.basic') ?></h2>

        <label>
            <p><?= Lang::get('boom::settings.navigation.nav') ?></p>

            <select name="visible_in_nav" id="visible_in_nav">
                <option value="1"<?php if ($page->isVisibleInNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                <option value="0"<?php if ( ! $page->isVisibleInNav()): ?> selected="selected"<?php endif ?>>No</option>
            </select>
        </label>

        <label>
            <p><?= Lang::get('boom::settings.navigation.cms') ?></p>

            <select name="visible_in_nav_cms" id="visible_in_nav_cms">
                <option value="1"<?php if ($page->isVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>Yes</option>
                <option value="0"<?php if ( ! $page->isVisibleInCmsNav()): ?> selected="selected"<?php endif ?>>No</option>
            </select>
        </label>
    </section>

    <?php if ($allowAdvanced): ?>
        <section id='advanced'>
            <h2><?= Lang::get('boom::settings.advanced') ?></h2>

            <label for="parent_id">Parent page</label>

            <input type="hidden" name="parent_id" value="<?= $page->getParentId() ?>">
            <ul class="boom-tree"></ul>
        </section>
    <?php endif ?>
</form>
