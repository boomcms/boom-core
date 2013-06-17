<div class="boom-tabs">
	<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
		<?
			if (isset($pagination)):
				echo "<div class='boom-pagination ui-helper-left'>", $pagination, "</div>";
			endif;
		?>
		
		<select id="boom-tagmanager-sortby-select" class="ui-helper-left" style="width: 98px">
			<option value="last_modified-desc" <? if ($sortby == 'last_modified-desc') echo "selected='selected'"; ?>>Most recent</option>
			<option value="last_modified-asc" <? if ($sortby == 'last_modified-asc') echo "selected='selected'"; ?>>Oldest</option>
			<option value="title-asc" <? if ($sortby == 'title-asc') echo "selected='selected'"; ?>>Title A-Z</option>
			<option value="title-desc" <? if ($sortby == 'title-desc') echo "selected='selected'"; ?>>Title Z-A</option>
			<option value="filesize-asc" <? if ($sortby == 'filesize-asc') echo "selected='selected'"; ?>>Size (smallest)</option>
			<option value="filesize-desc" <? if ($sortby == 'filesize-desc') echo "selected='selected'"; ?>>Size (largest)</option>
		</select>
	</div>
	<ul>
		<li><a href="#b-items-view-thumbs" class="boom-button" data-icon="ui-icon-boom-thumbnails">Thumbnails</a></li>
		<li><a href="#b-items-view-list" class="boom-button" data-icon="ui-icon-boom-list">List</a></li>
	</ul>

	<div id="b-items-view-list">
		<table width="100%">
			<tr>
				<th class="ui-helper-reset">
				</th>
				<th class="ui-helper-center" align="center">
					<?=__('Last edited')?>
				</th>
				<th>
					<?=__('Title')?>
				</th>
				<th>
					<?=__('Filesize')?>
				</th>
				<th>
					<?=__('Uploaded on')?>
				</th>
				<th>
					<?=__('Uploaded by')?>
				</th>
				<th>
					<?=__('Tags')?>
				</th>
			</tr>
			<? foreach ($assets as $asset): ?>
				<tr title='<?= $asset->description ?>'>
					<td class="ui-helper-reset">
						<input type="checkbox" class="b-items-select-checkbox ui-helper-reset" id="asset-list-<?=$asset->id?>" />
					</td>
					<td class="ui-helper-center" align="center">
						<label for="asset-<?=$asset->id?>"><?=date('M-j-Y', $asset->last_modified)?></label>
					</td>
					<td>
						<a href="#asset/<?=$asset->id?>"><img src="/media/boom/img/icons/16x16/icon_<?= $asset->type() ?>.gif" /> <?=$asset->title?></a>
					</td>
					<td>
						<?= Text::bytes($asset->filesize) ?>
					</td>
					<td>
						<?=date('M-j-Y', $asset->uploaded_time)?>
					</td>
					<td>
						<?= $asset->uploader->name ?>
					</td>
					<td>
						<span class='tags'>
							<?
								foreach($asset->tags->find_all() as $tag):
									echo "<a rel=​'ajax' name='#tag/", $tag->pk(), "' href='#tag/", $tag->pk(), "'>", $tag->name, " <br /></a>";
								endforeach
							?>​
						</span>
					</td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>

	<div id="b-items-view-thumbs" class="b-items-thumbs ui-helper-left">
		<? foreach ($assets as $asset): ?>
			<div class="boom-tagmanager-assets b-items-thumbs ui-helper-clearfix">
				<div class="thumb ui-corner-all">

					<input type="checkbox" class="b-items-select-checkbox ui-helper-reset" id="asset-thumb-<?=$asset->id?>" />

					<a href="#asset/<?=$asset->id?>" class='boom-tagmanager-thumb-link'>
						<img src="/asset/thumb/<?=$asset->id?>/100/100/85/1" />
						<span class="caption"><?=$asset->title?></span>
						<span class="caption-overlay"></span>
					</a>
				</div>
			</div>
		<? endforeach; ?>
	</div>
	<div style="padding: .5em 0 .5em .5em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-helper-right" style="margin: .5em .5em 0 0">
			<?=__('Total files')?>: <?= Num::format($total, 0) ?> | <?=__('Total size')?>: <?= Text::bytes($total_size) ?>
		</div>
	</div>
</div>