<?= view('boomcms::header', ['title' => trans('boomcms::asset.manager')]) ?>

<?= $manager ?>

<script type="text/javascript">
    window.onload = function() {
        $('body').assetManager();
    };
</script>

<?= view('boomcms::footer') ?>
