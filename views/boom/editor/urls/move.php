<p>
	Would you like to move the URL <?= $url->location ?>?
</p>

<? if ($url->is_primary AND ! $current->version()->page_deleted): ?>
	<p>
		<b>This URL is the primary URL for its page. If you move this URL its current page may become inaccessible.</b>
	</p>
<? endif; ?>
<br />
<br />

<? if ($current->version()->page_deleted): ?>
	<p>
		This URL is assigned to a page which has been deleted.
	</p>
<? endif ?>

<table>
	<tr>
		<th>
			Current Page
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
			<?= ($current->has_published_version())? 'Published' : 'Unpublished'; ?>
		</td>
		<td>
			<?= ($page->has_published_version())? 'Published' : 'Unpublished'; ?>
		</td>
	</tr>
</table>