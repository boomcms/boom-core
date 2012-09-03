<?php
/**
* RSS feed template.
* Displays a page and its children in RSS format.
*
* Rendered by Controller_Feeds::action_rss();
*
*********************** Variables **********************
*	$page			****	instance of Model_Page			****	The page being displayed as an RSS feed.
*	$children		****	array of Model_Page instances	****	Child pages to include as feed items.
********************************************************
*
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
?>
<rss version="2.0">
	<channel>
		<title><?= $page->title ?></title>
		<link><?= $page->url()?></link>
		<description>
			<?= htmlentities(Chunk::factory('text', 'standfirst')->text()) ?>
		</description>
		<language>en-gb</language>
		<pubDate><?= date('r', $page->visible_from) ?></pubDate>
		<lastBuildDate><?= date('r', time()) ?></lastBuildDate>
		
		<?
			foreach ($children as $p):
				?>
				<item>
					<guid>
						<?= $p->url() ?>
					</guid>
					<title>
						<![CDATA[ <?= html_entity_decode($p->title, ENT_QUOTES, 'UTF-8') ?> ]]>
					</title>
					<link>
						<?= $p->url() ?>
					</link>
					<description>
						<![CDATA[ 
							<?= html_entity_decode(strip_tags(Chunk::factory('text', 'standfirst')->text()), ENT_QUOTES, 'UTF-8') ?>
						]]>
					</description>
					<pubDate>
						<?= date('r', $p->visible_from) ?>
					</pubDate>
				</item>
				
				<?
			endforeach;
		?>
	</channel>
</rss>