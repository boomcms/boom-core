<div class="boom-box ui-widget ui-corner-all">
	<ul class="ui-helper-clearfix boom-tree b-tags-tree boom-tree-noborder">
		<? show_tags($tags, 0); ?>
	</ul>
</div>

<?
	function show_tags($tags, $parent)
	{
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
?>