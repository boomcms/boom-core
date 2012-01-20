<div id="slege-page-revisions">
	<div id="sledge-page-revisions-list">
		<table width="100%">
			<tbody>
				<?
				$count = $page->revisions->count_all();
				foreach ($page->revisions->order_by( 'id', 'desc' )->find_all() as $i => $revision):?>
					<tr>
						<td width="20"><input type="checkbox" id="revision-<?=$revision->id?>" class="sledge-page-revision-check ui-helper-reset"<?if ($revision->id == $page->version->id):?> checked="checked"<?endif?> /></td>
						<td>
							<label for="revision-<?=$revision->id?>">
								Version <?=$count-$i?>
								<?
									if ($revision->id == $page->published_vid):
										echo "<small><strong>(<em>Published version</em>)</strong></small>";
									endif;
								
									if ($revision->id == $page->active_vid):
										echo "<small><strong>(<em>Current version</em>)</strong></small>";
									endif;
								?>
							</label><br />
							<small><?= $revision->get_time() ?> by <?=($revision->person->id) ? $revision->person->getName() : 'Unknown'?></small>
						</td>
					</tr>
				<?endforeach;?>
			</tbody>
		</table>
	</div>

	<div id="sledge-page-revisions-options" style="border-top:1px solid #aaa;margin-top:1em">
		<button class="sledge-button-cancel sledge-button ui-helper-right">
			Cancel
		</button>
		With selected:
		<button id="sledge-button-multiaction-edit" class="sledge-button ui-button-text-icon">
			<span class="ui-button-icon-primary ui-icon ui-icon-wrench"></span>
			View/Edit
		</button>
		<button id="sledge-button-multiaction-publish" class="sledge-button ui-button-text-icon">
			<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
			Publish
		</button>
	</div>
</div>
	
