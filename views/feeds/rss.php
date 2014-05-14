<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?= $page->version()->title ?></title>
		<link><?= $page->url()?></link>
		<description>
			<?= htmlentities(Chunk::factory('text', 'standfirst')->text()) ?>
		</description>
		<language>en-gb</language>
		<pubDate><?= $page->getVisibleFrom()->format('r') ?></pubDate>
		<lastBuildDate><?= date('r', time()) ?></lastBuildDate>
		<atom:link href="<?= $page->url() ?>.rss" rel="self" type="application/rss+xml" />

		<? foreach ($pages as $p): ?>
			<item>
				<guid><?= $p->url() ?></guid>
				<title><?= filter_var($p->version()->title, FILTER_SANITIZE_SPECIAL_CHARS) ?></title>
				<link><?= $p->url() ?></link>
				<description><![CDATA[<?= htmlentities(strip_tags(Chunk::factory('text', 'standfirst', $p)->text()), ENT_QUOTES, 'UTF-8', false) ?>]]></description>
				<pubDate><?= $p->getVisibleFrom()->format('r') ?></pubDate>
			</item>
		<? endforeach; ?>
	</channel>
</rss>