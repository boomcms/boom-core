<p>
	Would you like to move the link <?= $link->location ?>?
</p>

<? if ($link->is_primary): ?>
	<p>
		<b>This link is the primary link for its page. If you move this link its current page may become inaccessible.</b>
	</p>
<? endif; ?>
<br />
<br />

<? if ( ! $current->loaded()): ?>
	<p>
		This link is assigned to a page which has been deleted.
	</p>
<? else: ?>
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
				<?= $current->title ?>
			</td>
			<td>
				<?= $page->title ?>
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
				<?= ($current->is_published())? 'Published' : 'Unpublished'; ?>
			</td>
			<td>
				<?= ($page->is_published())? 'Published' : 'Unpublished'; ?>
			</td>
		</tr>
	</table>
<? endif; ?>