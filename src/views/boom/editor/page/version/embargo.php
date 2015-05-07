<form class="b-form-settings">
        <p>
            Set a time when the draft changes to the page will become live.
        </p>

        <p>
            <strong>Note: this will set a time for the page's edits to become live. If you want to embargo the entire page you should use the <a href="#" class="visibility">visibility settings</a>.</strong>
        </p>

        <label>
            <?=Lang::get('Embargo until')?>
            <?= Form::input('embargoed_until', ($version->embargoed_until) ? date("d F Y h:i", $version->embargoed_until) : date("d F Y h:i", $_SERVER['REQUEST_TIME']), ['class' => 'boom-datepicker', 'id' => 'page-embargo']) ?>
        </label>
</form>
