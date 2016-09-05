<h1><?= trans('boomcms::asset.tags') ?></h1>

<ul class="b-tags">
    <li>
        <form class="b-tags-add" action="#">
            <input type="text" value="" class="b-tags-add-name" />
            <?= $button('plus', 'add-tag', ['type' => 'submit']) ?>
        </form>
    </li>
</ul>