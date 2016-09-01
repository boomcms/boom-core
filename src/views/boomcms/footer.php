    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            window.BoomCMS.init({
                <?php if (auth()->check()): ?>
                    user: new BoomCMS.Person(<?= auth()->user()->toJson() ?>),
                <?php endif ?>
                assetTypes: json_encode(Lang::get('boomcms::asset.type'))
            });
        })(jQuery);
        //]]>
    </script>
</body>
</html>
