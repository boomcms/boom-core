<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
	<? if (isset($pagination)): ?>
		<div class='ui-helper-left'><?= $pagination ?></div>
	<?endif ?>

	<select id="boom-tagmanager-sortby-select">
		<optgroup label="Direction">
			<option value="name-asc" <? if ($sortby == 'name-asc') echo "selected='selected'"; ?>>A - Z</option>
			<option value="name-desc" <? if ($sortby == 'name-desc') echo "selected='selected'"; ?>>Z - A</option>
		</optgroup>
	</select>
	</select>
</div>

<div id="b-items-view-list">
	<table>
		<? foreach ($people as $person): ?>
			<tr class="<?= Text::alternate('odd', 'even') ?>">
				<td width="10" class="ui-helper-reset">
					<input type="checkbox" class="b-people-select" data-id="<?= $person->id ?>" />
				</td>
				<td>
					<a href="#person/<?=$person->id?>"><?= $person->name ?></a>
				</td>
				<td>
					<?= $person->email ?>
				</td>
				<td>
					<span class='tags'>
						<? foreach($person->groups->find_all() as $group): ?>
							<a rel=​'ajax' name='#tag/<?= $group->id ?>' href='#tag/<?= $group->id ?>'><?= $group->name ?> &raquo;</a>
						<? endforeach ?>​
					</span>
				</td>
			</tr>
		<? endforeach ?>
	</table>
</div>