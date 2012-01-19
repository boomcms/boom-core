<div id="sledge-pagesettings-information">

	<table width="100%">
		<tbody>
			<tr>
				<td>Created on</td>
				<td><?= $page->first_version()->get_time(); ?></td>
			</tr>
			<tr>
				<td>Created by</td>
				<td>
					<?= $page->first_version()->person->getName(); ?>
				</tr>
			</tr>
			<tr>
				<td>Last modified</td>
				<td><?= $page->get_time(); ?></td>
			</tr>
			<tr>
				<td>Last modified by</td>
				<td>
					<?= $page->person->getName(); ?>
				</td>
			</tr>
			<tr>
				<td>Revisions</td>
				<td>This page has been edited a total of <?= $page->revisions->count_all() ?> times.</td>
			</tr>
		</tbody>
	</table>
</div>
