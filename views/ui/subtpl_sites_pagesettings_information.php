<div id="sledge-pagesettings-information">

	<table width="100%">
		<tbody>
			<tr>
				<td>Created on</td>
				<td><?= $page->getFirstVersion()->getAuditTime(); ?></td>
			</tr>
			<tr>
				<td>Created by</td>
				<td>
					<?= $page->getFirstVersion()->audit_person->getName(); ?>
				</tr>
			</tr>
			<tr>
				<td>Last modified</td>
				<td><?= $page->getAuditTime(); ?></td>
			</tr>
			<tr>
				<td>Last modified by</td>
				<td>
					<?= $page->person->getName(); ?>
				</td>
			</tr>
			<tr>
				<td>Revisions</td>
				<td>This page has been edited a total of <?= $edit_count ?> times.</td>
			</tr>
		</tbody>
	</table>
</div>
