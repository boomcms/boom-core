<div id="b-people-addgroup">
	<form onsubmit='return false;'>
		<p><?= Kohana::message('boom-people', 'addgroup1') ?></p>
		<p><?= Kohana::message('boom-people', 'addgroup2') ?></p>
        <select name="groups[]" multiple>
            <?php foreach($groups as $group): ?>
                <option value="<?= $group->getId() ?>"><?= $group->getName() ?></option>
            <?php endforeach ?>
        </select>
	</form>
</div>
