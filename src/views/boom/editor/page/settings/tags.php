<div id="b-tags" class="boom-tabs">
    <ul>
        <li><a href="#free">Free tags</a></li>
        <li><a href="#grouped">Grouped tags</a></li>
        <li><a href="#pages">Tagged pages</a></li>
    </ul>

    <section id="free">
        <ul class="b-tags-list" data-group="">
            <?php foreach ($freeTags as $tag): ?>
                <?= new BoomCMS\Core\UI\Tag($tag) ?>
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
        <ul class="b-tags-grouped">
            <?php foreach ($groups as $group): ?>
                <li>
                    <p><?= $group ?></p>

                    <ul class="b-tags-list" data-group="<?= $group ?>">
                        <?php if (isset($tags[$group])): ?>
                            <?php foreach ($tags[$group] as $tag): ?>
                                <?= new BoomCMS\Core\UI\Tag($tag) ?>
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

    <section id="pages">
        <p>You can other pages as being related to this page.</p>
        <p>This might be useful, for example, when your site contains blog templates and you need to define a relationship between blog posts and author pages.</p>
        <p>In this case you would tag a blog post with author pages.</p>

        <ul>
            <?php foreach ($relatedPages as $p): ?>
                <li>
                    <a href="#" data-page-id="<?= $p->getId() ?>">
                        <span class="title"><?= $p->getTitle() ?></span>
                        <span class="uri"><?= $p->url()->getLocation() ?></span>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>

        <?= $button('plus', 'Add related page', ['id' => 'b-tags-addpage']) ?>
    </section>
</div>
