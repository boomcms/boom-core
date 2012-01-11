<div id="slege-page-revisions">
	<div id="sledge-page-revisions-list">
		<table width="100%">
			<tbody>
				<?
				$count = count( $versions );
				foreach ($versions as $i => $version):?>
					<tr>
						<td width="20"><input type="checkbox" id="revision-<?=$version->id?>" class="sledge-page-revision-check ui-helper-reset"<?if ($version->id == $page->active_vid){?> checked="checked"<?}?> /></td>
						<td>
							<label for="revision-<?=$version->id?>">
								Version <?=$count-$i?>
								<?/*if ($version->id == $published_page->id) {?>
									<small><strong>(<em>Published version</em>)</strong></small>
								<?}*/?>
								<?if ($version->id == $page->active_vid) {?>
									<small><strong>(<em>Current version</em>)</strong></small>
								<?}?>
							</label><br />
							<small><?=date('j F Y H:i')?> by <?=($version->person->id) ? $version->person->getName() : 'Unknown'?></small>
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
	
