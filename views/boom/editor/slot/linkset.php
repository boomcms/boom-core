<div class="boom-tabs">
    <ul>
        <li><a href="#b-linkset-links">Links</a></li>
        <li id="b-linkset-title-tab"><a href="#b-linkset-title">Title</a></li>
    </ul>

    <div id="b-linkset-links">
        <ul>
            <?php foreach ($links as $link): ?>
                <li><a href='#' data-page-id="<?= $link->target_page_id ?>" data-url="<?= $link->url ?>"><?= $link->getLink()->getTitle() ?></a></li>
            <?php endforeach ?>
        </ul>

        <?php if (! count($links)): ?>
            <p class="none">This linkset does not contain any links.</p>
        <?php endif ?>

        <?= new Boom\UI\Button('add', 'Add link', array('id' => 'b-linkset-add', 'class' => 'b-button-withtext')) ?>
    </div>

    <div id="b-linkset-title">
        <input type="text" name="title" value="<?= $title ?>" />
    </div>
</div>