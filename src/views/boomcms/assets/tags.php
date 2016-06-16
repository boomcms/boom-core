<div id="b-tags">
    <ul class="b-tags-list">
        <?php foreach ($tags as $tag): ?>
            <li class='b-tag'>
                <span><?= $tag ?></span>
                <a href='#' class='fa fa-trash-o b-tag-remove' data-tag="<?= $tag ?>"></a>
            </li>
        <?php endforeach ?>
    </ul>

    <form class="b-tags-add">
        <input type="text" value="" class="b-tags-add-name" />
        <?= $button('plus', 'add-tag') ?>
    </form>
</div>
