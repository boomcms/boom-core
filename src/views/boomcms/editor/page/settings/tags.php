<div id="b-tags">
    <h1><?= Lang::get('boomcms::settings.tags.heading') ?></h1>

    <section id="free">
        <h2><?= Lang::get('Free tags') ?></h2>

        <ul class="b-tags-list" data-group="">
            <?php foreach ($freeTags as $tag): ?>
                <?= new BoomCMS\UI\Tag($tag) ?>
            <?php endforeach ?>

            <li class="b-tag">
                <form class="b-tags-add">
                    <input type="text" value="" class="b-tags-add-name" />
                    <?= $button('plus', 'Add tag') ?>
                </form>
            </li>
        </ul>
    </section>

    <section id="grouped">
        <h2><?= Lang::get('Grouped tags') ?></h2>

        <ul class="b-tags-grouped">
            <?php foreach ($groups as $group): ?>
                <li>
                    <p><?= $group ?></p>

                    <ul class="b-tags-list" data-group="<?= $group ?>">
                        <?php if (isset($tags[$group])): ?>
                            <?php foreach ($tags[$group] as $tag): ?>
                                <?= new BoomCMS\UI\Tag($tag) ?>
                            <?php endforeach ?>
                        <?php endif ?>

                        <li class="b-tag">
                            <form class="b-tags-add">
                                <input type="text" value="" class="b-tags-add-name" />
                                <?= $button('plus', 'Add tag') ?>
                            </form>
                        </li>
                    </ul>
                </li>
            <?php endforeach ?>

            <li class="b-tags-newgroup">
                <p>Add a new tag group</p>

                <form>
                    <input type="text" value="" class="b-tags-newgroup-name" />
                    <?= $button('plus', 'Add tag group') ?>
                </form>
            </li>
        </ul>
    </section>
</div>
