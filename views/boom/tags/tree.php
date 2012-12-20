<?php
/**
* Subtemplate for a tag tree.
* Essentially the same for the page tree but with different formatting and 'page' replaced with 'tag'.
* Rendered by Controller_Tag::action_tree()
*
*********************** Variables **********************
*	$tags		****	array
*	$state		****	string		****	collapsed or expanded
*	$base_uri	****	string		****	URI to use for tag links. Allows changing links for tag manager or asset manager.
********************************************************
*
*/
?>
<div class="sledge-box ui-widget ui-corner-all">
	<ul class="ui-helper-clearfix sledge-tree s-tags-tree sledge-tree-noborder">
		<?
			if ( ! empty($tags)):
				show_tags($tags, $root);
			endif;
		?>
	</ul>
</div>

<?
function show_tags($tags, $parent)
{
	global $state;

	foreach ($tags[$parent] as $id => $name)
	{
		echo "<li id='t$id'><a rel='$id' id='tag_" , $id , "' href='#tag/$id";
		echo "'>$name</a>\n";

		if (isset($tags[$id]))
		{
			echo "<ul  class='ui-helper-hidden'>";
			show_tags($tags, $id);
			echo "</ul>";
		}
	}
}