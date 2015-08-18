    <?= View::make('boom::header', ['title' => 'Settings']) ?>

    <div id="b-settings-manager">
        <div id="b-topbar" class="b-toolbar">
            <?= $menuButton() ?>
            <?= $menu() ?>
        </div>
    </div>

    <?= $boomJS ?>
    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();
        })(jQuery);
        //]]>
    </script>
</body>
</html>