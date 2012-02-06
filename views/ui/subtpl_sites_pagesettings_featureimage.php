<?php
/**
* Feature image tab of page settings.
*
* Rendered by:	Controller_Cms_Page_Settings::action_feature()
* Submits to:	Controller_Cms_Page::action_save()
*
*********************** Variables **********************
*	$person	****	Instance of Model_Person	****	The active user.
*	$page	****	Instance of Model_Page		****	The page being edited.
********************************************************
*
*/
?>
<?
	if ($person->can( 'view', $page, 'feature_image' )):
		?>
			<div id="sledge-pagesettings-featureimage">

				<?
					if (!$page->has_image() && $person->can( 'edit', $page, 'feature_image' )):
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
		<?
	endif;
?>
