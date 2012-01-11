<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<?
$this->start_rid = $_REQUEST['tag_rid'];

if (!@$_REQUEST['selected_tag_rid']) {
	$this->selected_ancestors = array();
	$this->selected = array();
	$this->selected_tag = 0;
} else {
	$selected_tag = O::fa('tag', $_REQUEST['selected_tag']);
	$this->selected_ancestors = $selected_tag->get_ancestortags();
	$this->selected = array($selected_tag->rid);
	$this->selected_tag = $selected_tag->rid;
}
?>
<div id="page-tags" class="ui-helper-clearfix" style="margin-bottom:.8em">
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all"> 
			<p style="margin: .5em;">
				<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
				Click on a tag name to select it.
			</p> 
		</div>
	</div>
	<br />
	<ul class="sledge-tree">
	<?
		if (!function_exists("recurse")) {

			function recurse($tag) {
					
				$ki = Kohana::Instance();

				if ($tag->hidden_from_tree != 't') {?>
					<li>
						<?if (in_array($tag->rid, $ki->selected)) {?>
							<a rel="ajax" id="tag_<?=$tag->rid?>" class="current" href="#tag/<?=$tag->rid?>"><?=$tag->name?></a>
						<?} else {?>
							<a rel="ajax" id="tag_<?=$tag->rid?>" href="#tag/<?=$tag->rid?>"><?=$tag->name?></a>
						<?}
		
						if (Tag::gettags($tag->rid)) {?>
							<ul class="<?=(!in_array($tag->rid, $ki->selected_ancestors) ? 'ui-helper-hidden' : '')?>"><?
								foreach (Tag::gettags($tag->rid) as $tag2) {
									recurse($tag2);
								}
							?></ul><?			
						}?>
					</li><?
				}
			}
		}

		foreach ($tags = Tag::gettags($this->start_rid) as $tag) {

			recurse($tag);
		}
	?>
	</ul>
</div>

