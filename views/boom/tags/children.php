<div class="boom-box ui-widget ui-corner-all">
	<ul class="ui-helper-clearfix boom-tree s-tags-tree boom-tree-noborder">
		<? foreach ($tags as $tag): ?>
			<li id='t<?= $tag->id ?>'>
				<a rel='<?= $tag->id ?>' id='tag_<?= $tag->id ?>' href='#tag/<?= $tag->id ?>'><?= $tag->name ?></a>	
			</li>
		<? endforeach; ?>
	</ul>
</div>