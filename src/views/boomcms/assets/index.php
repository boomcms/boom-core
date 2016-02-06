<?= view('boomcms::header', ['title' => 'Assets']) ?>

<?= $manager ?>

<script type="text/javascript">
    window.onload = function() {
        $('body').assetManager();
    };
</script>

<?= view('boomcms::footer') ?>
