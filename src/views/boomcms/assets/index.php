    <?= view('boomcms::header', ['title' => 'Assets']) ?>

    <?= $manager ?>

    <?= $boomJS ?>
    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();

            $('body')
                .ui()
                .assetManager();
        })(jQuery);
        //]]>
    </script>
</body>
</html>
