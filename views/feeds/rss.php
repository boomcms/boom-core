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
			<?= htmlentities( $page->get_slot( 'text', 'standfirst') ) ?>
		</description>
		<language>en-us</language>
		<pubDate><?= date('D, M j, Y H:i', $page->visible_from) ?></pubDate>
		<lastBuildDate><?= date('D, j M Y H:i:s', time()) ?></lastBuildDate>
		<docs/>
		<managingEditor/>
		<WebMaster/>
		
		<?
			foreach ($children as $p):
				?>
				<item>
				<title>
					<![CDATA[ <?= $p->title ?> ]]>
				</title>
				<link>
					<?= $p->page->url() ?>
				</link>
				<description>
					<![CDATA[ 
						<?= utf8_encode( $p->get_slot( 'text', 'standfirst', null, null, false ) ) ?>
					]]>
				</description>
				<pubDate><?= date('D, M j, Y H:i', $p->visible_from) ?></pubDate>
				</item>
				
				<?
			endforeach;
		?>
	</channel>
</rss>