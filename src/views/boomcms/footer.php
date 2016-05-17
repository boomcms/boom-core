    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            window.BoomCMS.init({
                user: new BoomCMS.Person(<?= auth()->user()->toJson() ?>)
            });
        })(jQuery);
        //]]>
    </script>
</body>
</html>
