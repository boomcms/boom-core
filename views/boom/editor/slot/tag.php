<div id="b-select-tag">
	<h2>Select tag</h2>
	<p>Enter the name of a tag below to feature it here</p>
	<input id="b-tags-add-name" />
</div>

<div id="b-selected">
	<h2>Selected Tag</h2>
	<? if ($current_tag->loaded()): ?>
		<p><?= $current_tag->name ?></p>
	<? else: ?>
		<p>No tag is currently selected</p>
	<? endif ?>
</div>