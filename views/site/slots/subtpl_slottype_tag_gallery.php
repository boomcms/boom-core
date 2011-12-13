<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$toplevel_tag_rid = $this->toplevel_tag_rid = isset($_REQUEST['toplevel_tag']) && (ctype_digit($_REQUEST['toplevel_tag']) || is_int($_REQUEST['toplevel_tag'])) ? $_REQUEST['toplevel_tag'] : $toplevel_rid;

	if (!isset($this->assets) || empty($this->assets)) {
		$this->chunk_tag_rid = $target->rid;
		NH_Library::get_assets();
	}
	$n_assets = count($this->assets);
?>
<div class="library-results rel-toplevel-<?=@$toplevel_tag_rid;?>">
<div id="chunk-gallery" class="clearfix">
	<?if ($n_assets <1 && $this->page_model == 'cms_page') {?>
		<p>
			<?if (isset($this->search_text)) {?>
				There are no assets which match your search criteria.
			<?}else{?>
				There are no assets currently assigned to this tag.
			<?}?>
		</p>
	<?}?>
	<?foreach ($this->assets as $i => $asset) {
		// I hate this, but dave wants the type name standardised :/
		$type = ($asset->type == 'msword' ? 'Word' : $asset->type);

		if ($i %3 == 0) {?>
			<div class="row clearfix">
		<?}?>
		<div class="item<?if (($i+1) %3 == 0){?> last<?}?>">
			<dl>
				<dt class="image">
					<?if ($asset->type == 'msword') {?>
						<img src="/sledge/img/icons/ms_word.jpg" width="100" height="100" alt="<?=$asset->title?>" />
					<?}else{?>
						<img src="/_ajax/call/asset/get_asset/<?=$asset->rid?>/100/100/85/1" alt="<?=$asset->title?>" />
					<?}?>
				</dt>
				<dd class="description">
					<p><?=$asset->title?> <a href="/_ajax/call/asset/get_asset/<?=$asset->rid?>/0/0/0/0/0/1"><?=$type?> <?=strtolower($asset->get_size())?></a></p>
					<p class="download">
						<a href="/_ajax/call/asset/get_asset/<?=$asset->rid?>/0/0/0/0/0/1">
							<img src="/img/download.png" alt="Download <?=$asset->title?>">
						</a>
					</p>		
				</dd>
			</dl>
		</div>
		<?if (($i+1) %3 == 0 || ($i+1) == $n_assets) {?>
			</div>
		<?}?>
	<?}?>
</div>
</div>
