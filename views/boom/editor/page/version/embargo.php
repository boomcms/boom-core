<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>

        <label>
            <?=__('Embargo until')?>
            <?= Form::input('embargoed_until', ($version->embargoed_until)? date("d F Y h:i", $version->embargoed_until) : date("d F Y h:i", $_SERVER['REQUEST_TIME']), array('class' => 'boom-datepicker', 'id' => 'page-embargo')) ?>
        </label>
</form>