<div id="b-items-view-list">
	<table class="b-table">
		<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Email address</th>
				<th>Groups</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($people as $person): ?>
				<tr data-person-id="<?= $person->getId() ?>">
					<td width="10">
						<input type="checkbox" class="b-people-select" />
					</td>
					<td>
						<a href="/boomcms/person/<?= $person->getId() ?>"><?= $person->getName() ?></a>
					</td>
					<td>
						<?= $person->getEmail() ?>
					</td>
					<td>
						<span class='tags'>
							<?php foreach ($person->getGroups() as $group): ?>
								<a rel=​'ajax' name='<?= $group->getId() ?>' href='/boomcms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?> &raquo;</a>
							<?php endforeach ?>​
						</span>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
