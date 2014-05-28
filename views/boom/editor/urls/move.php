<p>
	Would you like to move the URL <?= $url->location ?>?
</p>

<? if ($url->is_primary && ! $currentisDeleted()): ?>
	<p>
		<b>This URL is the primary URL for its page. If you move this URL its current page may become inaccessible.</b>
	</p>
<? endif; ?>
<br />

<? if ($currentisDeleted()): ?>
	<p>
		<b>This URL is assigned to a page which has been deleted.</b>
	</p>
<? endif ?>

<br />
<table>
	<tr>
		<th>
			Current Page<? if ($currentisDeleted()): ?> (deleted) <? endif; ?>
		</th>
		<th>
			New Page
		</th>
	</tr>
	<tr>
		<td>
			<?= $current->getTitle() ?>
		</td>
		<td>
			<?= $page->getTitle() ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= ($current->isVisible())? 'Visible' : 'Invisible'; ?>
		</td>
		<td>
			<?= ($page->isVisible())? 'Visible' : 'Invisible'; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= ucfirst($current->getCurrentVersion()->status()) ?>
		</td>
		<td>
			<?= ucfirst($page->getCurrentVersion()->status()) ?>
		</td>
	</tr>
</table>