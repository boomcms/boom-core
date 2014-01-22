<div id="b-items-view-list">
	<table>
		<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Email address</th>
				<th>Groups</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($people as $person): ?>
				<tr class="<?= Text::alternate('odd', 'even') ?>" data-person-id="<?= $person->id ?>">
					<td width="10">
						<input type="checkbox" class="b-people-select" />
					</td>
					<td>
						<a href="<?= Route::url('people-edit', array('controller' => 'person', 'action' => 'view', 'id' => $person->id)) ?>"><?= $person->name ?></a>
					</td>
					<td>
						<?= $person->email ?>
					</td>
					<td>
						<span class='tags'>
							<? foreach($person->groups->find_all() as $group): ?>
								<a rel=​'ajax' name='<?= $group->id ?>' href='/cms/people?group=<?= $group->id ?>'><?= $group->name ?> &raquo;</a>
							<? endforeach ?>​
						</span>
					</td>
				</tr>
			<? endforeach ?>
		</tbody>
	</table>
</div>