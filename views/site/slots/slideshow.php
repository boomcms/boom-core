<?php
/**
* Template to display a slideshow chunk
* Unlike most of the templates in this directory this one is currently being used in Sledge 3.
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>

<div id="work-images" class="chunk-tag rel-toplevel-<?= $chunk->id;?>">
	<div class="work-images-slideshow">
		<?
			if (count($slides)):
				foreach ($slides as $slide):
					if ( $slide->url ):
						echo "<a href='$slide->url'><img src='/asset/", $slide->asset_id, "' /></a>";
					else:
						echo "<img src='/asset/", $slide->asset_id, "' />";
					endif;
				endforeach;
			endif;
		?>
	</div>
	<div id="work-images-nav" class="work-images-nav"></div>
	<script type="text/javascript">
	//<![CDATA[
		$("#work-images-nav").append("<ul></ul>");
		$('#work-images').cycle({
			fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
			speedIn: 1000,
			speedOut: 1000,
			timeout: 5000,
			pager: '#work-images-nav ul',
			slideExpr: 'img',
			pagerAnchorBuilder : function(index, slide){
				return '<li><a href="#"></a></li>';
			}
		});
	//]]>
	</script>
</div>
