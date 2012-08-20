<?
	$tags = $page->tags();

	if ( ! empty($tags)):
		foreach ($tags as & $tag):
			$tag = htmlentities($tag->name, ENT_QUOTES);
			$tag = "<a href='/blog/tag/" . urlencode($tag) . "'>$tag</a>";
		endforeach;	

		$tags = implode(", ", $tags);
		echo "<h3>Tags: </h3>$tags";
	endif;
?>