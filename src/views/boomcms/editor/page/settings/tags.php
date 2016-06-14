<div id="b-tags">
    <h1><?= trans('boomcms::settings.tags.heading') ?></h1>

    <ul id="b-page-tags">
        <?php foreach (array_keys($all) as $group): ?>
            <li>
                <h2><?= $group ?></h2>

                <ul class="b-tags-list" data-group="<?= $group ?>">
                    <?php foreach ($all[$group] as $tag): ?>
                        <li>
                            <a href="#"<?php if (in_array($tag->getId(), $tags)): ?> class='active'<?php endif ?>>
                                <span><?= $tag->getName() ?></span>
                                <span class='fa fa-trash-o remove'></span>
                            </a>
                        </li>
                    <?php endforeach ?>

                    <li>
                        <form class="b-tags-add">
                            <input type="text" value="" class="b-tags-add-name" />
                            <?= $button('plus', 'Add tag') ?>
                        </form>
                    </li>
                </ul>
            </li>
        <?php endforeach ?>

        <li class="b-tags-newgroup">
            <h2>Add a new tag group</h2>

            <form>
                <input type="text" value="" class="b-tags-newgroup-name" />
                <?= $button('plus', 'Add tag group') ?>
            </form>
        </li>
    </ul>
</div>
