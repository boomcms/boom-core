<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
	<?
		if (isset($pagination)):
			echo "<div class='ui-helper-left'>", $pagination, "</div>";
		endif;
	?>

	<select id="boom-tagmanager-sortby-select" class="ui-helper-left" style="width: 98px">
		<optgroup label="Direction">
			<option value="name-asc" <? if ($sortby == 'name-asc') echo "selected='selected'"; ?>>A - Z</option>
			<option value="name-desc" <? if ($sortby == 'name-desc') echo "selected='selected'"; ?>>Z - A</option>
		</optgroup>
	</select>
	</select>
</div>

<div id="b-items-view-list" class="ui-helper-left">
	<table>
		<? foreach ($people as $person): ?>
			<tr>
				<td width="10" class="ui-helper-reset">
					<input type="checkbox" class="b-items-select-checkbox ui-helper-reset" id="person-list-<?=$person->id?>" />
				</td>
				<td>
					<a href="#person/<?=$person->id?>"><?= $person->name ?></a>
				</td>
				<td>
					<span class='tags'>
						<?
							foreach($person->groups->find_all() as $group):
								echo "<a rel=​'ajax' name='#tag/", $group->pk(), "' href='#tag/", $group->pk(), "'>", $group->name, " &raquo;</a>";
							endforeach
						?>​
					</span>
				</td>
			</tr>
		<? endforeach ?>
	</table>
</div>