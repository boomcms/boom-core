<div id="b-tags">
    <ul class="b-tags-list">
        <?php foreach ($tags as $tag): ?>
            <li class='b-tag'>
                <span><?= $tag ?></span>
                <a href='#' class='b-tag-remove' data-tag="<?= $tag ?>"></a>
            </li>
        <?php endforeach ?>

        <li class="b-tag">
            <form class="b-tags-add">
                <input type="text" value="" class="b-tags-add-name" />
                <?= new Boom\UI\Button('add', 'Add tag') ?>
            </form>
        </li>
    </ul>
</div>