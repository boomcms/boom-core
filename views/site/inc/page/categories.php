<?
	$cats = $page->get_tags('Categories');

	if ( ! empty($cats)):
		foreach ($cats as & $cat):
			$cat = htmlentities($cat->name, ENT_QUOTES);
			$cat = "<a href='/blog/category/" . urlencode($cat) . "'>$cat</a>";
		endforeach;	

		$cats = implode(", ", $cats);
		echo "<h3>Categories: </h3>$cats";
	endif;
?>