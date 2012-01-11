<?
if (!isset($_GET['rid'])) exit;
$published_page = O::fa('page',$_GET['rid']);
$current_page = O::f('page_v', $_GET['vid']);
$count = O::f('page_v')->where("rid = {$_GET['rid']} and autosave != 't'")->find_all()->count();
?>
<div id="slege-page-revisions">
	<div id="sledge-page-revisions-list">
		<table width="100%">
			<tbody>
				<?foreach (O::f('page_v')->where("rid = {$_GET['rid']} and autosave != 't'")->orderby('audit_time','desc')->find_all() as $i => $page_v) {?>
					<? $person = O::fa('person',$page_v->audit_person); ?>
					<tr>
						<td width="20"><input type="checkbox" id="revision-<?=$page_v->id?>" class="sledge-page-revision-check ui-helper-reset"<?if ($page_v->id == $current_page->id){?> checked="checked"<?}?> /></td>
						<td>
							<label for="revision-<?=$page_v->id?>">
								Version <?=$count-$i?>
								<?if ($page_v->id == $published_page->id) {?>
									<small><strong>(<em>Published version</em>)</strong></small>
								<?}?>
								<?if ($page_v->id == $current_page->id) {?>
									<small><strong>(<em>Current version</em>)</strong></small>
								<?}?>
							</label><br />
							<small><?=date('j F Y H:i')?> by <?=($person->rid) ? $person->firstname.' '.$person->lastname : 'Unknown'?></small>
						</td>
					</tr>
				<?}?>
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
	
