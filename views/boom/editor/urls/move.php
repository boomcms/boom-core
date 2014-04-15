<p>
	Would you like to move the URL <?= $url->location ?>?
</p>

<? if ($url->is_primary && ! $current->version()->page_deleted): ?>
	<p>
		<b>This URL is the primary URL for its page. If you move this URL its current page may become inaccessible.</b>
	</p>
<? endif; ?>
<br />

<? if ($current->version()->page_deleted): ?>
	<p>
		<b>This URL is assigned to a page which has been deleted.</b>
	</p>
<? endif ?>

<br />
<table>
	<tr>
		<th>
			Current Page<? if ($current->version->page_deleted): ?> (deleted) <? endif; ?>
		</th>
		<th>
			New Page
		</th>
	</tr>
	<tr>
		<td>
			<?= $current->version()->title ?>
		</td>
		<td>
			<?= $page->version()->title ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= ($current->is_visible())? 'Visible' : 'Invisible'; ?>
		</td>
		<td>
			<?= ($page->is_visible())? 'Visible' : 'Invisible'; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?= ucfirst($current->version()->status()) ?>
		</td>
		<td>
			<?= ucfirst($page->version()->status()) ?>
		</td>
	</tr>
</table>