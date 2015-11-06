<p>
	Would you like to move the URL <?= $url->getLocation() ?>?
</p>

<?php if ($url->isPrimary() && !$current->isDeleted()): ?>
	<p>
		<b>This URL is the primary URL for its page. If you move this URL its current page may become inaccessible.</b>
	</p>
<?php endif ?>
<br />

<?php if ($current->isDeleted()): ?>
	<p>
		<b>This URL is assigned to a page which has been deleted.</b>
	</p>
<?php endif ?>

<br />
<table>
	<tr>
		<th>
			Current Page<?php if ($current->isDeleted()): ?> (deleted) <?php endif ?>
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
			<?= ($current->isVisible()) ? 'Visible' : 'Invisible' ?>
		</td>
		<td>
			<?= ($page->isVisible()) ? 'Visible' : 'Invisible' ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= ucfirst($current->getCurrentVersion()->getStatus()) ?>
		</td>
		<td>
			<?= ucfirst($page->getCurrentVersion()->getStatus()) ?>
		</td>
	</tr>
</table>
