<div id='b-tags-search'>
    <input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
    <ul class="b-tags-list">
        <?php if (isset($tags)): ?>
            <?php foreach ($tags as $t): ?>
                <li class="b-tag">
                    <span><?= $t ?></span>
                    <a href="#" class="fa fa-trash-o b-tag-remove" data-tag="<?= $t ?>"></a>
                </li>
            <?php endforeach ?>
        <?php endif ?>
    </ul>
</div>