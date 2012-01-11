<p>Are you sure you want to delete this page? This cannot be recovered.</p>

<?
	$this->page = O::fa('page', (int) $_GET['rid']);

	$msg = '';

	if (count($this->page->get_child_pages())) {

		$count = 0; 
		$titlelist = '';
		
		foreach ($this->page->get_child_pages() as $pi) {
			$count++;
			$titlelist .= "<li>" . $pi->title . "</li>";
		}
		$titlelist = preg_replace("/\, $/", "", $titlelist);
		
		$msg = "<p><strong>Warning:</strong><br />Deleting this page will make it's " . $count . " child ";

		$msg .= ($count < 2) ? 'page' : 'pages';

		$msg .=  " inaccessible:</p><div id=\"sledge-page-delete-children\"><ul>" . $titlelist . "</ul></div>";
	}
	
	echo $msg;
?>

<p>Click 'Okay' to delete, or 'Cancel' to keep the page.</p>
