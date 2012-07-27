<div class="news-feed">
	<h3>Latest Tweets</h3>
	<ul>
		<? foreach ($tweets as $tweet): ?>
			<li class='twitter'>
				<span class='date'><?= Date::fuzzy_span( strtotime($tweet->created_at)) ?></span>
				<?= Text::twitter($tweet->text) ?>
			</li>
		<? endforeach; ?>
		<li class="followus"><a href="http://twitter.com/<?= $screen_name ?>">Follow us on Twitter!</a></li>
	</ul>
</div>