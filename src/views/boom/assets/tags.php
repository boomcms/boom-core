<div id="b-tags">
    <ul class="b-tags-list">
        <?php foreach ($tags as $tag): ?>
            <li class='b-tag'>
                <span><?= $tag ?></span>
                <a href='#' class='fa fa-trash-o b-tag-remove' data-tag="<?= $tag ?>"></a>
            </li>
        <?php endforeach ?>

        <li class="b-tag">
            <form class="b-tags-add">
                <input type="text" value="" class="b-tags-add-name" />
                <?= new BoomCMS\UI\Button('plus', 'Add tag') ?>
            </form>
        </li>
    </ul>
</div>
