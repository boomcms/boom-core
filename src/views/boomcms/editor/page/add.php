<div id="b-page-add-prompt">
    <h1><?= trans('boomcms::page.add.heading') ?> - <?= $page->getTitle() ?></h1>

    <p><?= trans('boomcms::page.add.question') ?></p>

    <div class="buttons">
        <button data-parent="<?= $page->getId() ?>">
            <span><?= trans('boomcms::page.add.child') ?></span>
        </button>

        <button data-parent="<?= $page->getParent()->getId() ?>">
            <span><?= trans('boomcms::page.add.sibling') ?></span>
        </button>

        <button>
            <span><?= trans('boomcms::page.add.cancel') ?></span>
        </button>
    </div>
</div>