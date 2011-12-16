<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$tag_v = O::fa('tag',$this->start_rid);
if ($this->selected[0] !== NULL && $this->selected[0] != '') {
	$tag = O::fa('tag',$this->selected[0]);
	$this->selected_ancestors = $tag->get_ancestortags();
} else {
	$this->selected_ancestors = array();
}
//$this->basetag = O::fa('tag')->find_by_parent_rid_and_name($this->roottag->rid, 'Assets');
$this->smartfolderstag = Tag::find_tag($this->basetag->rid, 'Smart folders', 'f', false, true);
?>

<ul class="tags tree">
	<?
		function recurse($tag, $recursion_count=1) {
			if ($tag->hidden_from_tree != 't') {
				$ki = Kohana::Instance();
				$tags = Tag::gettags($tag->rid);
				?>
				<?if ($tag->name == 'Rubbish') {?>
					<li class="rubbish">
				<?} else {?>
					<li>
				<?}?>
				<?if ($ki->recursion_count == 1 and $ki->toplevel) {
					 if (in_array($tag->rid, $ki->selected_ancestors)) {
						$child_tag = Tag::gettags($tag->rid);
						if (count($child_tag)) {
							$open_close_class = 'close';
						} else {
							$open_close_class = 'open';
						}
					} else {
						$open_close_class = 'close';
					}?>
					<a<?=(!sizeof($tags))?' rel="ajax"':''?> class="toplevel <?=$open_close_class;?>" id="tag_<?=$tag->rid?>" href="#tag/<?=$tag->rid?>">
							<span>
								<?=$tag->name?>
							</span>
					</a>
					<?$ki->toplevel_rid = $tag->rid?>
				<?} else if (in_array($tag->rid, $ki->selected)) {?>
					<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><a rel="ajax" id="tag_<?=$tag->rid?>" class="current" href="#tag/<?=$tag->rid?>"><?=$tag->name?></a></td>
					</tr>
					</table>
				<?} else {?>
					<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><a rel="ajax" id="tag_<?=$tag->rid?>" href="#tag/<?=$tag->rid?>"><?=$tag->name?></a></td>
					</tr>
					</table>
				<?}

				if (sizeof($tags)) {
					// RW NOTE: $ki->modal is ambiguous, and should only be set to true if used if the tree itself is being used in a modal (IE folder/tag management)
					?><ul class="tree <?=(!$ki->modal && !in_array($tag->rid, $ki->selected_ancestors)?' hidden':'')?>"><?
					$ki->recursion_count++;
					foreach ($tags as $tag2) {
						recurse($tag2, $ki->recursion_count);
					}
					?></ul><?			
				}
				$ki->recursion_count = $recursion_count;
				?>	
				</li>
				<?
			}
		}
	?>
	<?
		foreach(explode(",", $this->start_rid) as $start_rid) {
			$child_tags = Tag::gettags($start_rid);
			if (!sizeof($child_tags)) {
				$child_tags = array(O::fa('tag', $start_rid));
			}
			foreach ($child_tags as $thistag) {
				$this->recursion_count = 1;
				$this->toplevel_rid = 0;
				recurse($thistag, 1);
			}
		}
	?>
</ul>
