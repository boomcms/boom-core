<?php
/**
* Shows a tree of pages on the site to select a page to feature.
*
* Rendered by Controller_Cms_Chunk::action_feature()
*
*********************** Variables **********************
*	$page			****	Instance of Model_Page	****	The page which is being edited.
********************************************************
*
*/
?>
<div style="margin-bottom: .6em">
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all"> 
			<p style="margin: .5em;">
				<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
				Select a page to feature below.
			</p> 
		</div>
	</div>
	<br />

	<input type="hidden" name="parent_id" value="<?=$page->mptt->parent_id?>">
		<ul class="boom-tree">
			<li><a id="page_5" href="/" rel="5">Home</a></li>
		</ul>
</div>