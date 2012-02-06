<?php
/**
* SEO tab of page settings.
*
* Rendered by:	Controller_Cms_Page_Settings::action_seo()
* Submits to:	Controller_Cms_Page::action_save()
*
*********************** Variables **********************
*	$person	****	Instance of Model_Person	****	The active user.
*	$page	****	Instance of Model_Page		****	The page being edited.
********************************************************
*
*/
?>
<form id="sledge-form-pagesettings-seo" name="pagesettings-seo">
	<div id="sledge-pagesettings-seo" class="sledge-tabs">
		<ul>
			<li><a href="#sledge-pagesettings-seo-basic">Basic</a></li>
			<li><a href="#sledge-pagesettings-seo-advanced">Advanced</a></li>
		</ul>

		<div id="sledge-pagesettings-seo-basic">
			<table width="100%">
				
				<?if ($person->can( 'view', $page, 'description' )): ?>
					<tr>
						<td style="vertical-align:top">
							<label for="description" class="ui-helper-clearfix">
								<span class="ui-helper-left" style="padding-top:2px">
									Description
								</span>
								<span class="ui-icon ui-helper-left ui-icon-help sledge-tooltip" title="A description of the description field."></span>
							</label>
						</td>
						<td>
							<? if ($person->can( 'edit', $page, 'description' )): ?>
								<textarea id="description" name="description" class="sledge-textarea"><?=$page->get_description() ?>
								</textarea>
							<? 
								else:
									echo $page->get_description();
								endif;
							?>
						</td>
					</tr>
				<? endif; ?>	
		        
				<?if ($person->can( 'view', $page, 'keywords' )): ?>
					<tr>
						<td style="vertical-align:top">
							<label for="keywords" class="ui-helper-clearfix">
								<span class="ui-helper-left" style="padding-top:2px">
									Keywords
								</span>
								<span class="ui-icon ui-icon-help ui-helper-left sledge-tooltip" title="Keywords description: please separate your keywords with a comma."></span>
							</label>
						</td>
						<td>
							<? if ($person->can( 'edit', $page, 'keywords' )): ?>
								<textarea name="keywords" id="keywords" class="sledge-textarea"><?=$page->get_keywords() ?>
								</textarea>
							<?
								else:
									echo $page->get_keywords();
								endif;
							?>
						</td>
					</tr>
				<? endif; ?>
			</table>
		</div>

		<div id="sledge-pagesettings-seo-advanced">
			<table width="100%">
				<? if ($person->can( 'view', $page, 'indexed' )): ?>
					<tr>
						<td>Allow page indexing</td>
						<td>
							<? if ($person->can( 'edit', $page, 'indexed' )): ?>
								<select name="indexed">
									<option <?if ($page->indexed) echo "selected='selected' "; echo "value='1'>Yes</option>"; ?>
									<option <?if (!$page->indexed) echo "selected='selected' "; echo "value='0'>No</option>"; ?>
								</select>
							<?
								else:
									echo ($page->indexed)? 'Yes' : 'No';
								endif;
							?>
						</td>
					</tr>
				<? endif; ?>
			
				<?if ($person->can( 'view', $page, 'hidden_from_search_results' )): ?>
					<tr>
						<td>Hidden from search results</td>
						<td>
							<? if ($person->can( 'edit', $page, 'hidden_from_search_results' )): ?>
								<select name="hidden_from_search_results">
									<option value="0">No</option>
									<option value="1"<?if ($page->hidden_from_search_results):?> selected="selected"<? endif; ?>>Yes</option>
								</select>
							<?
								else:
									echo ($page->hidden_from_search_results)? 'Yes' : 'No';
								endif;
							?>
						</td>
					</tr>
				<? endif; ?>
			</table>
		</div>
	</div>
</form>
