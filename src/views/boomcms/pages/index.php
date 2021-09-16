<?= view('boomcms::header', ['title' => 'Pages']) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div id="b-pages">
        <h1>
            <?= trans('boomcms::pages.heading') ?>
            <span style="color: #ff34cc;">
                <i class="fa fa-refresh" onClick="window.location.reload();" aria-hidden="true"></i>
            </span>
        </h1>
        <ul class='boom-tree'></ul>
    </div>
</main>
<script type="text/javascript">
    window.onload = function() {
        $('#b-pages > .boom-tree').pageManager();
    };
</script>

<?= view('boomcms::footer') ?>