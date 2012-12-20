<div class="sledge-box ui-widget ui-corner-all">
	<ul class="ui-helper-clearfix sledge-tree s-tags-tree sledge-tree-noborder">
		<? foreach ($tags as $tag): ?>
			<li id='t<?= $tag->id ?>'>
				<a rel='<?= $tag->id ?>' id='tag_<?= $tag->id ?>' href='#tag/<?= $tag->id ?>'><?= $tag->name ?></a>	
			</li>
		<? endforeach; ?>
	</ul>
</div>