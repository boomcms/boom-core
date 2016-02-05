<h1><?= trans('boomcms::urls.add.heading') ?></h1>

<h2><?= trans('boomcms::urls.add.pointers') ?></h2>

<ul>
	<li><?= trans('boomcms::urls.add.lowercase') ?></li>
	<li><?= trans('boomcms::urls.add.nospaces') ?></li>
	<li><?= trans('boomcms::urls.add.hyphens') ?></li>
	<li><?= trans('boomcms::urls.add.nosurprises') ?></li>
	<li><?= trans('boomcms::urls.add.keywords') ?></li>
</ul>

<form id="b-form-addurl" name="form-addurl">
	<input type='hidden' name='page' value='<?= $page->getId() ?>' />

	<label>
        <span><?= trans('boomcms::urls.add.new') ?></span>
		<input type='text' name='url' id='url' placeholder='<?= trans('boomcms::urls.add.placeholder') ?>' />
	</label>
</form>
