<?= view('boomcms::header', ['title' => trans('boomcms::template-manager.title')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container"></main>

<script type="text/template" id="b-template-pages">
    <h1><?= trans('boomcms::template-manager.pages-heading') ?> <%= template.getName() %></h1>

    <table id="b-templates" class="b-table tablesorter">
        <thead>
            <tr>
                <th><?= trans('boomcms::template-manager.page-title') ?></th>
                <th><?= trans('boomcms::template-manager.url') ?></th>
                <th><?= trans('boomcms::template-manager.visible') ?></th>
                <th><?= trans('boomcms::template-manager.last-edited') ?></th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>
</script>

<script type="text/template" id="b-template-page">
    <td><%= page.getTitle() %></td>
    <td><a href='<%= page.getUrl() %>'><%= page.getUrl() %></a></td>
    <td><%= page.isVisible() ? "<?= trans('boomcms::template-manager.page-visible') ?>" : "<?= trans('boomcms::template-manager.page-invisible') ?>" %></td>
    <td><time datetime="<%= page.getLastEdited() %>"></time></td>
</script>

<script type="text/template" id="b-template-list">
    <div>
        <h1><?= trans('boomcms::template-manager.title') ?></h1>

        <table id="b-templates" class="b-table tablesorter">
            <thead>
                <tr>
                    <th><?= trans('boomcms::template-manager.name') ?></th>
                    <th><?= trans('boomcms::template-manager.description') ?></th>
                    <th><?= trans('boomcms::template-manager.theme') ?></th>
                    <th><?= trans('boomcms::template-manager.filename') ?></th>
                    <th><?= trans('boomcms::template-manager.pages') ?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody></tbody>
        </table>
    </div>
</script>

<script type="text/template" id="b-template-row">
    <td><input type='text' name='name' value="<%= template.getName() %>" /></td>
    <td><input type='text' name='description' value="<%= template.getDescription() %>" /></td>
    <td><%= template.getTheme() %></td>
    <td><input type="text" name="filename" value="<%= template.getFilename() %>" /></td>
    <td>
        <a href='#template/<%= template.getId() %>/pages'><?= trans('boomcms::template-manager.pages-view') ?>
    </td>
    <td><?= $button('trash', 'delete-template', ['class' => 'delete small']) ?>
</script>

<script type="text/javascript" src="/vendor/boomcms/boom-core/js/template-manager.js"></script>

<script type="text/javascript">
    window.onload = function() {
        new BoomCMS.TemplateManager({
            templates: <?= Template::findAll() ?>
        });
    };
</script>

<?= view('boomcms::footer') ?>
