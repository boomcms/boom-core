<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?
if ($chunk->title == '') { $chunk->title = 'Quicklinks'; }
$links = O::fa('linkset_links')->orderby('sequence', 'asc')->find_all_by_chunk_linkset_rid((int)$chunk->rid);

$i = 0;
?>
<div id="quicklinks">
        <h3>
                <?if (get_class($this) == 'Ajax_Controller') {?>
                        <input type="text" id="linkset_title" value="<?= $chunk->title;?>" class="trans" style="font-size:108%;" />
                        <span id="linkset_title_error" class="error">&nbsp;</span>
                <? } else {
                        echo $chunk->title;
                }?>
        </h3>

	<div>
		<ul>
			<?foreach($links as $link){ $i++; ?>
				<li<?=$i==sizeof($links)?' style="border:0px"':'';?>>
					<?
						if ($link->target_page_rid) {
							// internal link
							$target_page = O::f($this->page_model, $link->target_page_rid);?>
							<a href="<?=$target_page->absolute_uri();?>"<?if($link->target_page_rid != ''){echo' rel="'.$link->target_page_rid.'"';}?>><?=$target_page->title;?> &raquo;</a>
						<?} else {
							// external link?>
							<a href="<?=$link->uri;?>"><?=$link->name;?> &raquo;</a>
						<?}
					?>
				</li>
			<?}?>
		</ul>
	</div>
</div>
