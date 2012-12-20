<div class="boom-tabs">
	<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
		<?
			if (isset($pagination)):
				echo "<div class='boom-pagination ui-helper-left'>", $pagination, "</div>";
			endif;
		?>

		<?
			/*
			Hello.
			The two select boxes below originaly had class="boom-selectbox ui-helper-left".
			This was removed on 2012/03/21 as the javascript broke it found items of this class.
			*/
		?>
		<select id="boom-tagmanager-sortby-select" class="ui-helper-left" style="width: 98px">
			<optgroup label="Sort">
				<option value="last_modified" <? if ($sortby == 'last_modified') echo "selected='selected'"; ?>>Date</option>
				<option value="title" <? if ($sortby == 'title') echo "selected='selected'"; ?>>Title</option>
				<option value="filesize" <? if ($sortby == 'filesize') echo "selected='selected'"; ?>>Filesize</option>
			</optgroup>
		</select>
		<select id="boom-tagmanager-order-select" class="ui-helper-left" style="width: 130px">
			<optgroup label="Order">
				<option value="desc" <? if ($order == 'desc') echo "selected='selected'"; ?>>Descending</option>
				<option value="asc" <? if ($order == 'asc') echo "selected='selected'"; ?>>Ascending</option>
			</optgroup>
		</select>
	</div>
	<ul>
		<li><a href="#b-items-view-thumbs">Thumbnails</a></li>
		<li><a href="#b-items-view-list">List</a></li>
	</ul>

	<div id="b-items-view-list">
		<table>
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
						<a href="#asset/<?=$asset->id?>"><img src="/media/boom/img/icons/16x16/icon_<?= $asset->get_type() ?>.gif" /> <?=$asset->title?></a>
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
								foreach($asset->get_tags(NULL, FALSE) as $tag):
									echo "<a rel=​'ajax' name='#tag/", $tag->pk(), "' href='#tag/", $tag->pk(), "'>", $tag->name, " &raquo;</a>";
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
			<div class="boom-tagmanager-assets s-items-thumbs ui-helper-clearfix">
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
		<div id="b-items-checkactions" class="ui-widget-content">
			With <span id="boom-tagmanager-amount-checked"></span> selected:
		</div>
		<div id="b-items-multiactons" class="ui-widget-content">
			<button id="b-button-multiaction-edit" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-wrench">
				<?=__('View')?>/<?=__('Edit')?>
			</button>
			<button id="b-button-multiaction-delete" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-trash">
				<?=__('Delete')?>
			</button>
			<button id="b-button-multiaction-download" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-arrowreturn-1-s">
				<?=__('Download')?>
			</button>
			<button id="b-button-multiaction-tag" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-tag">
				<?=__('Add Tags')?>
			</button>
			<button id="b-button-multiaction-clear" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-cancel">
				<?=__('Clear Selection')?>
			</button>
		</div>
	</div>
</div>
