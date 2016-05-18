    <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function () {
            window.BoomCMS.init({
				user: '<?= auth()->user()->toJson() ?>'
			});

            $('body').pageEditor({
                page_id : <?= $page->getId() ?>,
                editable : <?= (int) ($editor->isEnabled() && Gate::allows('edit', $page)) ?>,
            });
        });
        //]]>
    </script>
</body>
</html>
