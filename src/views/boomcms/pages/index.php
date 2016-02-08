<?= view('boomcms::header', ['title' => 'Pages']) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div id="b-pages">
        <h1><?= trans('boomcms::pages.heading') ?></h1>
        <ul class='boom-tree'></ul>
    </div>
</main>

<script type="text/javascript">
    window.onload = function () {
        $('#b-pages > .boom-tree').pageManager();
    };
</script>

<?= view('boomcms::footer') ?>
