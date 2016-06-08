    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            window.BoomCMS.init({
                <?php if (auth()->check()): ?>
                    user: new BoomCMS.Person(<?= auth()->user()->toJson() ?>)
                <?php endif ?>
            });
        })(jQuery);
        //]]>
    </script>
</body>
</html>
