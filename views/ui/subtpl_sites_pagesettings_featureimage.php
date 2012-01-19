<div id="sledge-pagesettings-featureimage">

	<?
		if (!$page->has_image()):
			?>
				<p>This page currently has no associated feature image.</p>
				<p>
					<button id="sledge-featureimage-add" class="sledge-button">
						Add feature image
					</button>
				</p>
			<?
		else:
			echo "<img src='/asset/", $page->image->id, "' />";
		endif;
	?>
</div>
