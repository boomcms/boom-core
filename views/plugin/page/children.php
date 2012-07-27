<div class="library-pagination clearfix">
	<?= $pagination ?>
</div>
<div class="list">
	<ul>
		<? foreach ($pages as $p): ?>
			<li>
				<h3><a href="<?=$p->url()?>"><?=$p->title?></a></h3>
				<p class="date"><?= date('d F Y', $p->visible_from);?></p>
				<a href="<?=$p->url()?>">
					<? // TODO: News page's image goes here. ?>
				</a>
				<p style="clear: both;">
					<?= Chunk::factory('text', 'standfirst', $p )->editable( FALSE ); ?>
				
					<a href="<?=$p->url()?>" title="<?=$p->title?>">READ MORE &raquo;</a>
				</p>
				<p class="add-comment"><a href="<?=$p->url()?>#post-comment">Add a comment</a></p>
			
				<?
					$tweet_this = urlencode("Check out this cool link: " . $p->url() );
				?>
				<p class="tweet"><a href="http://twitter.com/home/?status=<?=$tweet_this?>">Tweet this</a></p>
			</li>
		<? endforeach; ?>
	</ul>
</div>
<div class="library-pagination clearfix">
	<?= $pagination ?>
</div>