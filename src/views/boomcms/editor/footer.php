    <script type="text/javascript" src="/vendor/boomcms/boom-core/js/cms.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function () {
            $.boom.init();

            $('body').pageEditor({
                page_id : <?= $page->getId() ?>,
                editable : <?= (int) ($editor->isEnabled() && ($auth->check('editContent', $page) || $page->wasCreatedBy($person))) ?>,
            });
        });
        //]]>
    </script>
</body>
</html>
