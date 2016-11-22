    <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function () {
            window.BoomCMS.init({
                user: new window.BoomCMS.Person(<?= auth()->user()->toJson() ?>)
            });

            $('body').pageEditor({
                page: new window.BoomCMS.Page(<?= $page->toJson() ?>),
                editable: <?= (int) ($editor->isEnabled() && Gate::allows('edit', $page)) ?>
            });
        });
        //]]>
    </script>
</body>
</html>
