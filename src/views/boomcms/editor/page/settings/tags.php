<div id="b-page-tags">
    <h1><?= trans('boomcms::settings.tags.heading') ?></h1>

    <ul class="b-tags">
        <?php if (!isset($all[''])): ?>
            <li>
                <h2><?= trans('boomcms::settings.tags.free') ?></h2>
                <ul data-group=""></ul>
            </li>
        <?php endif ?>

        <?php foreach (array_keys($all) as $group): ?>
            <li>
                <h2><?= $group ? $group : trans('boomcms::settings.tags.free') ?></h2>

                <ul data-group="<?= $group ?>">
                    <?php foreach ($all[$group] as $tag): ?>
                        <li>
                            <a data-tag="<?= $tag->getId() ?>" href="#"<?php if (in_array($tag->getId(), $tags)): ?> class='active'<?php endif ?>>
                                <span><?= $tag->getName() ?></span>
                                <span class='fa fa-times remove'></span>
                                <span class='fa fa-plus add'></span>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endforeach ?>
    </ul>

    <div class="b-tags-newgroup">
        <h2><?= trans('boomcms::settings.tags.new-group') ?></h2>

        <form>
            <input type="text" value="" placeholder="<?= trans('boomcms::settings.tags.new-group-placeholder') ?>" />
            <?= $button('plus', 'add-tag-group') ?>
        </form>
    </div>

    <script type="text/template" id="b-tags-add">
        <form class="create-tag">
            <input type="text" value="" class="b-tags-add-name" placeholder="<?= trans('boomcms::settings.tags.new-tag-placeholder') ?>" />
            <?= $button('plus', 'add-tag', ['tabindex' => -1]) ?>
        </form>
    </script>

    <?= view('boomcms::tags.tag') ?>
</div>
