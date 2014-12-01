<div id="b-tags" class="boom-tabs">
    <ul>
        <li><a href="#free">Free tags</a></li>
        <li><a href="#grouped">Grouped tags</a></li>
    </ul>

    <section id="free">
	<ul class="b-tags-list" data-group="">
            <?php foreach ($freeTags as $tag): ?>
                <?= new Boom\UI\Tag($tag) ?>
            <?php endforeach ?>

            <li class="b-tag">
                <form class="b-tags-add">
                    <input type="text" value="" class="b-tags-add-name" />
                    <?= new Boom\UI\Button('add', 'Add tag') ?>
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
                                <?= new Boom\UI\Tag($tag) ?>
                            <?php endforeach ?>
                        <?php endif ?>

                        <li class="b-tag">
                            <form class="b-tags-add">
                                <input type="text" value="" class="b-tags-add-name" />
                                <?= new Boom\UI\Button('add', 'Add tag') ?>
                            </form>
                        </li>
                    </ul>
                </li>
            <?php endforeach ?>

            <li class="b-tags-newgroup">
                <p>Add a new tag group</p>

                <form>
                    <input type="text" value="" class="b-tags-newgroup-name" />
                    <?= new Boom\UI\Button('add', 'Add tag group') ?>
                </form>
            </li>
        </ul>
    </section>
</div>