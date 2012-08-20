<?
	$authors = $page->authors();

	if ( ! empty($authors)):
		foreach ($authors as & $author):
			$author = htmlentities($author->name, ENT_QUOTES);
		endforeach;	

		$authors = implode(", ", $authors);
		echo "<span class='author'>by $authors</span>";
	endif;
?>